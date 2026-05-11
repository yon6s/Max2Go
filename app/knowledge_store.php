<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/project_knowledge.php';
require_once __DIR__ . '/project_store.php';

function stage_labels(): array
{
    return [
        'all' => '全部模块',
        'lead' => '线索与约访',
        'space' => '房源匹配与分割建议',
        'pricing' => '价格测算与报价空间',
        'recap' => '到访录音复盘',
        'proposal' => '客户方案生成',
        'negotiation' => '谈判话术助手',
        'contract' => '合同草案助手',
        'dashboard' => '管理看板与复盘沉淀',
    ];
}

function knowledge_types(): array
{
    return ['项目概况', '竞品说辞', '成交案例', '丢单复盘', '商务条件', '合同规则', '常见异议', '房源说明', '管理看板'];
}

function normalize_stages(array|string|null $stages): string
{
    if (!is_array($stages)) {
        $stages = $stages ? explode(',', (string)$stages) : ['all'];
    }
    $allowed = array_keys(stage_labels());
    $clean = array_values(array_unique(array_filter($stages, static fn ($stage) => in_array($stage, $allowed, true))));
    return $clean === [] ? 'all' : implode(',', $clean);
}

function knowledge_context(string $projectKey, string $stage): string
{
    $pdo = db();
    if (!$pdo instanceof PDO) {
        return '';
    }

    try {
        install_knowledge_table($pdo);
        $stmt = $pdo->prepare(<<<SQL
SELECT type, title, content, stages, priority
FROM knowledge_items
WHERE enabled = 1
  AND project_key IN ('general', :project_key)
  AND (stages = 'all' OR FIND_IN_SET('all', stages) OR FIND_IN_SET(:stage, stages))
ORDER BY priority ASC, updated_at DESC
LIMIT 12
SQL);
        $stmt->execute(['project_key' => $projectKey, 'stage' => $stage]);
        $items = $stmt->fetchAll();
    } catch (Throwable) {
        return '';
    }

    if (!$items) {
        return '';
    }

    $blocks = [];
    $length = 0;
    foreach ($items as $item) {
        $content = trim((string)$item['content']);
        if ($content === '') {
            continue;
        }
        $block = "【{$item['type']}｜{$item['title']}｜优先级{$item['priority']}】\n{$content}";
        $length += mb_strlen($block);
        if ($length > 12000) {
            break;
        }
        $blocks[] = $block;
    }

    return implode("\n\n---\n\n", $blocks);
}

function seed_meilanhu_knowledge(PDO $pdo): int
{
    install_knowledge_table($pdo);

    $title = '美兰湖竞品产品力对抗说辞（2025年4月）';
    $exists = $pdo->prepare('SELECT id FROM knowledge_items WHERE project_key = ? AND title = ? LIMIT 1');
    $exists->execute(['meilanhu', $title]);
    if ($exists->fetch()) {
        return 0;
    }

    $content = <<<'TEXT'
一、中集美兰城 vs MAX科技园
户型产品：中集美兰城户型偏大不可切割，最小约145㎡，标准层多为263-400㎡，不利中小客户进驻；得房率原约70%，画小后下降至约63%，户型进深长、采光差；设计灵活性差。MAX美兰湖小中大户型灵活搭配，最小可至约100㎡，得房率高、进深合理、户型方正、易分割，适合成长型企业逐步扩租。

成本与费用：中集美兰城物业费约16元/㎡/月，空调仅覆盖8点至18点；租户加班需自装空调或另付费用；停车位约400元/月，含立体车位。MAX美兰湖物业费约12元/㎡/月，全天可调节、按需计量；车位约300元/月，不固定可停平层；附加服务含阳台绿植免费养护。

装修与硬件：中集美兰城公区虽宽但装修一般，套内水泥裸顶无喷漆；电费约1.05元/度；无走廊空调，环境感受偏冷清；过道较长。MAX美兰湖公区设计有文化感，走廊设空调；套内标准统一，白色腻子顶，整洁明亮；电费约0.8元/度；公区绿化丰富，配空中阳台、外窗大，通透采光优越。

二、地产闵虹·之所智慧方洲 vs MAX科技园
户型产品：闵虹项目尚未开园，预招商无法落地签约；最小户型约197㎡，改小户需上报审批，流程慢；主力户型进深短且中柱阻隔，布局困难；实际得房率低于75%。MAX美兰湖已运营，即租即用；无审批障碍，快速响应客户分割需求；户型规整、进深合理、无干扰立柱，空间利用率高。

成本与费用：闵虹项目停车费约500元/月，含部分立体车位；空调未覆盖租区，电费约1.05元/度。MAX美兰湖停车费约300元/月，配比合理，全部平层可停；空调支持全天分时使用，节能高效；整体使用成本更具性价比。

装修与硬件：闵虹项目分毛坯/标交，毛坯无空调、新风，标交需加价；户型异形/配电房靠近租区，存在噪音问题；部分楼栋开窗方式传统，通风差。MAX美兰湖统一装修交付标准，VRV空调、新风、照明配套齐全；室内安静、结构科学、机电噪音隔离；全楼大开窗和阳台绿植，办公环境更佳。

三、国盛药谷 vs MAX科技园
国盛药谷当前运营重心偏向销售任务，招商动力不足；项目属104板块，属性差别大，暂无客户强对比，必要时略提即可。

四、MAX美兰湖核心对抗亮点
户型灵活：多样面积，支持自由分割。
成本可控：物业、停车、能耗成本更低。
环境优越：空中绿化、景观阳台、舒适光线。
硬件统一：高标准统一交付，拎包入驻。
服务人性化：全天空调、绿植养护、租户体验好。
时效优势：即可签约、即租即用，无等待。

使用规则：面对客户竞品比较时，不攻击竞品，用适配度、总成本、交付确定性、使用体验表达。涉及价格、费用、面积、得房率等数字时，表达为“约”“以现场和正式文件为准”。如客户未提竞品，不主动制造竞品焦虑，可自然嵌入MAX优势。
TEXT;

    $stmt = $pdo->prepare(<<<SQL
INSERT INTO knowledge_items (project_key, type, title, content, stages, priority, enabled)
VALUES (:project_key, :type, :title, :content, :stages, :priority, 1)
SQL);
    $stmt->execute([
        'project_key' => 'meilanhu',
        'type' => '竞品说辞',
        'title' => $title,
        'content' => $content,
        'stages' => 'tour,objection,recap',
        'priority' => 1,
    ]);

    return 1;
}
