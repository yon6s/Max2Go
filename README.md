# MAX租赁 AI 工作台

面向园区招商租赁一线业务的 PHP 单页 AI 工作台。项目围绕带看、异议处理、价格测算、一线记录、合同生成、平面设计和短视频获客等真实业务动作，把一线经验、项目知识、确定性计算和 AI 生成能力整合到同一个 SaaS 化三栏界面中。

当前项目目标不是做一个泛用聊天工具，而是把 MAX 科技园租赁业务中可复用的流程、模板和判断沉淀成可执行的工作基础设施。

## 当前状态

- PHP 单页应用，入口：`public/index.php`
- 前端主逻辑：`public/assets/app.js`
- 前端样式：`public/assets/styles.css`
- 后端业务逻辑：`app/`
- API 入口：`public/api/`
- 本地运行目录：`storage/`
- 标准合同模板：`app/templates/contract_template.docx`

> `app/config.php`、`storage/`、合同模板和生成文件均属于本地/私有运行资源，不提交到 GitHub。

## 核心模块

### 1. 带看实战包

根据客户画像、竞品倾向和客户原话，生成带看前准备内容：

- 主推策略
- 破冰话术
- 竞品对比提醒
- 带看过程中的推进重点

### 2. 异议处理与逼定

针对客户常见卡点生成一线可用话术：

- 嫌租金贵
- 要更长免租期
- 物业费/停车费异议
- 交付时间疑虑
- 需要回去请示老板

### 3. 价格测算与报价空间

纯本地算法模块，不调用大模型。用于测算：

- 破底率
- J 回正年数
- 目标合同单价
- 审批底线价
- 建议开口价
- 客户可见报价卡

关键数字由本地 PHP/JS 公式计算，AI 不参与确定性计算。

### 4. 一线记录与客户洞察

用于沉淀真实一线记录，减少汇报失真：

- 录音转写
- 微信对话
- 带看备注
- 报价反馈
- 异议处理过程
- 成交/流失复盘

AI 输出会区分原始事实、业务员主观判断、可能被忽视的客户真实需求、成交/流失关键原因和下一步动作。生成后可手动保存到客户时间线。

### 5. 合同草案助手

纯本地合同生成模块，不调用大模型。基于公司制式 Word 模板生成可下载合同：

- 自动联动承租方、法人、联系人、通知地址等字段
- 自动计算租期、交付日期、免租期、付款节点
- 支持小数免租期，底层按日级别推演
- 先预览关键数字和租金计划，再生成 Word 合同
- 使用 PHP `ZipArchive` 解析并替换 `.docx` XML

合同模块是确定性生成器，不让 AI 自由改写法务模板。

### 6. AI 室内平面设计

轻量化空间规划辅助：

- 本地预览参考平面图
- 生成文字版空间布局建议
- 提供建筑学长、酷家乐、51建模网等第三方工具入口

当前不把平面图上传给大模型，避免多模态成本和调试复杂度。

### 7. 短视频获客助手

用于招商短视频内容生产和转化诊断：

- 爆款脚本
- 分镜拍摄建议
- 同城视频拆解
- 评论区回复
- 播放不错但无留资诊断
- 矩阵裂变文案

## 模型接口

支持三档切换：

- DeepSeek
- 通义千问
- 脱机演示模式（内置离线数据）

未配置 API Key 或演示时，可使用脱机演示模式，保证展示不中断。

## 本地预览

推荐使用项目内置 PHP：

```bash
cd /Users/wang/Documents/Codex/Max2Go
./bin/php -S 127.0.0.1:8088 -t public
```

访问：

```text
http://127.0.0.1:8088/
```

默认登录密码：

```text
max2026
```

如果 `8088` 无法访问，先检查端口和 PHP 服务状态，不要随意换端口。

## 配置文件

复制示例配置：

```bash
cp app/config.example.php app/config.php
```

常用配置项：

```php
'app_password' => 'max2026',
'ai_provider' => 'deepseek',
'deepseek_api_key' => '',
'deepseek_base_url' => 'https://api.deepseek.com',
'deepseek_model' => 'deepseek-v4-flash',
'qwen_api_key' => '',
'qwen_base_url' => 'https://dashscope.aliyuncs.com/compatible-mode/v1',
'qwen_model' => 'qwen-plus',
'demo_mode_when_no_key' => true,
'db_host' => '127.0.0.1',
'db_port' => '3306',
'db_socket' => '',
'db_name' => 'max_rental_ai',
'db_user' => 'max_rental_ai',
'db_password' => '',
```

## MySQL 与知识库

工作台使用 MySQL 保存项目知识库、项目资料等后台数据。进入网站后可通过“知识库管理”维护项目资料。

知识库支持项目隔离，后端会按项目读取 `general` 和当前项目资料，用于带看、异议处理、一线记录、短视频等 AI 生成模块。

## 部署提醒

服务器部署时尤其注意：

```php
'db_socket' => '',
```

本地 Mac/MAMP 可能使用 `/Applications/MAMP/tmp/mysql/mysql.sock`，但复制到 Linux/Windows 服务器后会导致 PDO 优先走 socket 并连接失败。服务端建议清空 `db_socket`，使用 `127.0.0.1:3306` TCP/IP 连接。

## 开发原则

- 价格测算、合同生成等确定性任务全部本地代码完成。
- AI 只用于语言理解、策略建议、话术生成和复盘洞察。
- 合同模块不调用 AI，不自由生成法务文本。
- 不把大段无关知识库注入所有模块，按需加载以节省 Token。
- 默认本地提交；重要节点或每日收尾再推送 GitHub。

## 常用验证

```bash
./bin/php -l app/contract_generator.php
./bin/php -l app/pricing.php
node --check public/assets/app.js
```

## 协作说明

项目协作规则和交接记录见：

- `.codex/project-notes.md`
- `docs/DEV_NOTES.md`

新 Agent 接手时，应优先阅读以上两个文件，再处理具体需求。
