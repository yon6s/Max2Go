# MAX租赁AI工作台

这是租赁一线业务员使用的单页 AI 工作台 MVP，适合先用于 OpenMAX AIPK 赛展示，后续再接入真实房源、合同和平面图资料。

## 当前功能

- 简单访问密码保护
- 客户基础信息录入
- 8 个业务流程模块：获客评分、约访采集、房源分割建议、到访复盘、客户方案、谈判话术、合同草案、管理看板
- DeepSeek API 后端调用，API Key 不暴露到浏览器
- 未配置 API Key 时自动使用演示模式

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
