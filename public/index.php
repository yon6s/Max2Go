<?php
declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if (hash_equals((string)app_config('app_password', 'max2026'), (string)$_POST['password'])) {
        $_SESSION['logged_in'] = true;
        csrf_token();
        header('Location: ./');
        exit;
    }
    $loginError = '密码不正确，请再试一次。';
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ./');
    exit;
}

$csrf = csrf_token();
require_once __DIR__ . '/../app/ai_client.php';
$aiMeta = ai_public_meta();
?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MAX租赁AI工作台</title>
    <link rel="stylesheet" href="assets/styles.css?v=<?= time() ?>">
</head>
<body>
<?php if (!is_logged_in()): ?>
    <main class="login-shell">
        <section class="login-panel">
            <p class="eyebrow">MAX科技园</p>
            <h1>MAX租赁AI工作台</h1>
            <p class="login-copy">一线租赁顾问从获客、约访、带看、方案、谈判到签约的AI辅助界面。</p>
            <form method="post" class="login-form">
                <label for="password">访问密码</label>
                <input id="password" name="password" type="password" autocomplete="current-password" autofocus>
                <?php if ($loginError !== ''): ?>
                    <p class="form-error"><?= htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
                <button type="submit">进入工作台</button>
            </form>
        </section>
    </main>
<?php else: ?>
    <div
        class="app"
        data-csrf="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"
        data-ai-meta="<?= htmlspecialchars(json_encode($aiMeta, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>"
    >
        <header class="topbar">
            <div>
                <p class="eyebrow">MAX科技园</p>
                <h1>MAX租赁AI工作台</h1>
            </div>
            <div class="top-actions">
                <label class="model-switch">
                    <span>所属项目</span>
                    <select id="projectSelect">
                        <option value="meilanhu" selected>MAX科技园（上海·美兰湖）</option>
                        <option value="general">MAX科技园（通用）</option>
                    </select>
                </label>
                <label class="model-switch">
                    <span>模型接口</span>
                    <select id="providerSelect"></select>
                </label>
                <button id="demoBtn" class="secondary-btn" type="button">载入演示配置</button>
                <button id="knowledgeBtn" class="secondary-btn" type="button">知识库管理</button>
                <a href="?logout=1" class="ghost-link">退出</a>
            </div>
        </header>

        <main class="workspace">
            <aside class="sidebar">
                <nav class="flow-nav" aria-label="业务流程"></nav>
            </aside>
            <section class="input-panel">
                <div class="panel-heading">
                    <div>
                        <p class="step-kicker">当前模块</p>
                        <h2 id="stageTitle"></h2>
                    </div>
                </div>
                <p id="stageDesc" class="stage-desc"></p>
                <div id="stageFields" class="field-grid"></div>
                <div class="generate-actions">
                    <button id="generateBtn" class="primary-btn">生成AI建议</button>
                    <button id="stopBtn" class="secondary-btn" style="display: none;">停止生成</button>
                </div>
            </section>
            <section class="result-panel">
                <div class="result-heading">
                    <div>
                        <p class="step-kicker">AI输出画布</p>
                        <h2>灵感与策略建议</h2>
                    </div>
                    <div class="result-actions">
                        <button id="saveRecapBtn" class="secondary-btn" type="button" hidden>保存到客户时间线</button>
                        <button id="timelineBtn" class="secondary-btn" type="button" hidden>查看时间线</button>
                        <button id="copyBtn" class="secondary-btn">复制</button>
                    </div>
                </div>
                <article id="resultBox" class="result-box">
                    请选择左侧流程模块，填写或勾选信息后生成。支持 DeepSeek 与通义千问接口切换；未配置 API Key 时会返回本地演示结果。
                </article>
            </section>
        </main>

        <section id="knowledgeModal" class="modal" aria-hidden="true">
            <div class="modal-backdrop" data-close-modal></div>
            <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="knowledgeTitle">
                <div class="modal-heading">
                    <div>
                        <p class="step-kicker">后台资料</p>
                        <h2 id="knowledgeTitle">知识库管理</h2>
                    </div>
                    <button class="secondary-btn" type="button" data-close-modal>关闭</button>
                </div>
                <div class="knowledge-layout">
                    <form id="knowledgeForm" class="knowledge-form">
                        <input type="hidden" id="knowledgeId">
                        <label>
                            所属项目
                            <select id="knowledgeProject">
                                <option value="meilanhu">MAX科技园（上海·美兰湖）</option>
                                <option value="general">MAX科技园（通用）</option>
                            </select>
                        </label>
                        <label>
                            资料类型
                            <select id="knowledgeType"></select>
                        </label>
                        <label>
                            标题
                            <input id="knowledgeTitleInput" placeholder="例：美兰湖竞品对抗-中集美兰城">
                        </label>
                        <label>
                            优先级
                            <select id="knowledgePriority">
                                <option value="1">高</option>
                                <option value="2" selected>中</option>
                                <option value="3">低</option>
                            </select>
                        </label>
                        <fieldset class="knowledge-stage-box">
                            <legend>适用模块</legend>
                            <div id="knowledgeStages" class="check-grid"></div>
                        </fieldset>
                        <label class="knowledge-enabled">
                            <input id="knowledgeEnabled" type="checkbox" checked>
                            启用这条资料
                        </label>
                        <label>
                            内容
                            <textarea id="knowledgeContent" placeholder="直接粘贴项目资料、竞品说辞、成交案例、丢单复盘等。"></textarea>
                        </label>
                        <div class="knowledge-actions">
                            <button class="primary-btn" type="submit">保存资料</button>
                            <button id="knowledgeResetBtn" class="secondary-btn" type="button">新建</button>
                            <button id="seedMeilanhuBtn" class="secondary-btn" type="button">导入美兰湖示例</button>
                        </div>
                    </form>
                    <div class="knowledge-list-wrap">
                        <div class="project-admin">
                            <div class="result-heading">
                                <div>
                                    <p class="step-kicker">项目配置</p>
                                    <h2>项目管理</h2>
                                </div>
                                <button id="projectResetBtn" class="secondary-btn" type="button">新建项目</button>
                            </div>
                            <form id="projectForm" class="project-form">
                                <input type="hidden" id="projectId">
                                <label>
                                    项目编码
                                    <input id="projectKey" placeholder="例：baoshan">
                                </label>
                                <label>
                                    项目名称
                                    <input id="projectName" placeholder="例：MAX科技园（上海·宝山）">
                                </label>
                                <label>
                                    排序
                                    <input id="projectSort" type="number" value="100">
                                </label>
                                <label class="knowledge-enabled">
                                    <input id="projectEnabled" type="checkbox" checked>
                                    启用
                                </label>
                                <button class="primary-btn" type="submit">保存项目</button>
                            </form>
                            <div id="projectList" class="project-list"></div>
                        </div>
                        <div class="result-heading">
                            <div>
                                <p class="step-kicker">已维护资料</p>
                                <h2>当前项目知识</h2>
                            </div>
                            <button id="refreshKnowledgeBtn" class="secondary-btn" type="button">刷新</button>
                        </div>
                        <div id="knowledgeList" class="knowledge-list"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script src="assets/max2go-data.js?v=<?= time() ?>"></script>
    <script src="assets/app.js?v=<?= time() ?>"></script>
<?php endif; ?>
</body>
</html>
