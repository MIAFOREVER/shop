# Mia Jewelry WooCommerce Store

一个简单的珠宝 WooCommerce 独立站模板，适合本地开发、二次改主题、后续部署到 WordPress 主机。

## 包含内容

- WordPress + MariaDB + WP-CLI 的 Docker Compose 环境
- 自动安装并启用 WooCommerce
- 自定义珠宝主题 `mia-jewelry`
- 初始化脚本会创建首页、商店页配置和示例珠宝商品
- 默认启用演示支付方式和固定运费，方便本地下单测试
- 首页包含 Hero、分类入口、商品区和品牌信任区

## 快速启动

本项目推荐用 Docker 运行。需要先安装 Docker Desktop，或者在服务器上安装 Docker Engine + Docker Compose。

先复制环境变量并修改后台密码：

```bash
cp .env.example .env
```

然后启动并初始化：

```bash
bash scripts/setup.sh
```

默认访问地址：

- 店铺首页：http://localhost:8080
- WordPress 后台：http://localhost:8080/wp-admin

默认后台账号来自 `.env`：

- 用户名：`admin`
- 密码：`change-me-now`

第一次启动后请立刻修改密码。

## 常用命令

```bash
docker compose up -d
docker compose down
docker compose logs -f wordpress
docker compose run --rm wpcli mia seed
```

## Docker 生产部署

准备一台 VPS 或云服务器，并把域名 A 记录解析到服务器公网 IP。服务器需要开放 80 和 443 端口。

拉取代码：

```bash
git clone https://github.com/MIAFOREVER/shop.git
cd shop
```

创建生产环境变量：

```bash
cp .env.production.example .env.production
```

编辑 `.env.production`，至少改掉：

```env
SITE_DOMAIN=你的域名
LETS_ENCRYPT_EMAIL=你的邮箱
MYSQL_PASSWORD=强数据库密码
MYSQL_ROOT_PASSWORD=强 root 密码
WP_ADMIN_PASSWORD=强后台密码
WP_ADMIN_EMAIL=你的邮箱
```

首次部署：

```bash
bash scripts/deploy-init.sh
```

后续更新代码后重启：

```bash
git pull
docker compose --env-file .env.production -f docker-compose.prod.yml up -d
```

生产环境由 Caddy 自动申请 HTTPS 证书。WordPress、MariaDB 不直接暴露公网端口，只通过 Caddy 对外提供访问。

## Vercel 说明

这个项目是完整的 WordPress + WooCommerce + MariaDB，需要 PHP、数据库和可写上传目录，不适合直接部署到 Vercel。Vercel 更适合做 Headless 前端：例如 Vercel 跑 Next.js，另外一台服务器跑 WordPress/WooCommerce API。

## 目录说明

```text
wp-content/themes/mia-jewelry/   自定义主题
wp-content/mu-plugins/           初始化用 WP-CLI 命令
wp-content/uploads/              本地上传文件，默认不进 Git
deploy/Caddyfile                 生产部署 HTTPS 反向代理配置
```

## 后续上线建议

- 更换真实域名、店铺名称和 Logo
- 配置 PayPal / Stripe 等支付方式
- 配置运费区域、税费、邮件模板和隐私政策
- 用真实商品图替换示例远程图片
- 生产环境关闭调试，并使用正式 WordPress 主机或云服务器
