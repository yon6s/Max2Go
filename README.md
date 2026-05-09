# Max2Go｜MAX租赁AI工作台

Max2Go 是面向 MAX 科技园租赁一线业务员的 AI 工作台，把线索、约访、房源匹配、价格测算、到访复盘、客户方案、谈判、合同和管理复盘放到一个单页工具里。

项目当前定位：先服务 OpenMAX AIPK 赛展示和美兰湖项目试点，后续逐步扩展为多园区可维护的租赁业务系统。

## 当前功能

- 简单访问密码保护
- 客户基础信息录入
- 8 个业务流程模块：线索与约访、房源分割建议、价格测算、到访复盘、客户方案、谈判话术、合同草案、管理看板
- DeepSeek API 后端调用，API Key 不暴露到浏览器
- 未配置 API Key 时自动使用演示模式
- 项目管理：可新增多个园区项目
- 知识库管理：可维护项目概况、竞品说辞、成交案例、商务条件等
- Excel 模板驱动价格测算：保留公式可维护性，网页负责输入输出

## 目录结构

```text
app/                  PHP后端、配置、提示词、知识库、价格测算
public/               网站入口、前端脚本、样式、API接口
deploy/               Nginx和数据库建表示例
docs/                 项目文档
data/                 原始资料归档
storage/              运行中生成的测算Excel等文件
```

## 本地预览

```bash
php -S 127.0.0.1:8088 -t public
```

然后访问：

```text
http://127.0.0.1:8088
```

默认演示密码：

```text
max2026
```

## 配置 DeepSeek

复制配置文件：

```bash
cp app/config.example.php app/config.php
```

修改 `app/config.php`：

```php
'app_password' => '换成你的访问密码',
'deepseek_api_key' => '填入你的 DeepSeek API Key',
'deepseek_model' => 'deepseek-v4-flash',
'db_host' => '127.0.0.1',
'db_name' => '你的数据库名',
'db_user' => '你的数据库用户名',
'db_password' => '你的数据库密码',
```

## 知识库管理

先在宝塔创建一个 MySQL 数据库，把数据库名、用户名、密码填入 `app/config.php`。进入网站后点击右上角“知识库管理”，可以新增、编辑、删除项目资料。

第一次使用可以点击“导入美兰湖示例”，系统会自动创建知识库表并导入一条美兰湖竞品资料。

## Excel价格测算

价格测算模块使用 `app/templates/pricing_template.xlsx` 作为模板。网页会写入关键输入单元格，并读取破底率、J回正年数等输出。

如需服务器自动重算 Excel 公式，建议 Debian 安装 LibreOffice：

```bash
apt install libreoffice
```

未安装 LibreOffice 时，系统仍会生成本次测算 Excel，并使用后端备用公式输出关键指标。

## Debian 12 + LNMP 部署建议

建议部署目录：

```text
/var/www/max-rental-ai
```

Nginx 站点示例在：

```text
deploy/nginx-max-rental-ai.conf
```

部署后可先用端口访问：

```text
http://154.64.236.216:8088
```

## 后续迭代

- 接入 MySQL 保存客户档案和生成记录
- 上传合同模板并生成合同条款草案
- 上传整层平面图，建立 A/B/C/D 可分割区域库
- 增加录音转文字接口
- 增加管理看板真实数据

## 项目文档

- [需求说明](docs/需求说明.md)
- [功能规划](docs/功能规划.md)
- [价格测算逻辑](docs/价格测算逻辑.md)
- [部署说明](docs/部署说明.md)
- [比赛汇报思路](docs/比赛汇报思路.md)
- [开发记录](docs/开发记录.md)
