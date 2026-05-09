# Max2Go 项目协作说明

本项目遵循全局省额度协作偏好：少扫描、少解释、先看相关文件，必要时再扩大范围。

## 项目现状

- 项目是 PHP 单页应用，入口在 `public/index.php`。
- 前端主要逻辑在 `public/assets/app.js`，样式在 `public/assets/styles.css`。
- 后端业务文件在 `app/`，接口在 `public/api/`。
- 本地 Git 元数据使用 `.max2go-git`，不是标准 `.git`。
- GitHub 不需要每次小改都推送；重要节点或每日收尾再推送。
- `app/config.php` 存放本地密钥和数据库配置，不提交。
- `storage/` 是运行生成文件目录，不提交。
- 标准合同模板本地放在 `app/templates/contract_template.docx`，不提交。

## 工作方式

- 开始任务时先阅读与需求直接相关的文件。
- 不扫描 `node_modules/`、`storage/`、历史 zip、生成合同、Excel 原始分析文件，除非需求明确相关。
- 改代码前简短说明要动哪些区域。
- 小步实现，小步验证。
- 完成后只汇报：改了什么、验证了什么、还有什么风险。
- 默认只本地提交；除非用户要求、重要节点完成或每日收尾，不主动推送 GitHub。

## 常用验证

- PHP 语法：`./bin/php -l 路径`
- 前端语法：`node --check public/assets/app.js`
- 本地预览：`./bin/php -S 127.0.0.1:8088 -t public`

## 当前重点模块

- 模型接口：DeepSeek、通义千问、演示结果三档可切换。
- 价格测算：当前基准为 `max-rental-ai-excel-pricing` 版。
- 合同助手：基于公司制式 Word 模板生成合同；不调用 AI；先预览关键数字，再生成下载。
