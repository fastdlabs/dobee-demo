# Upgrading dobee-demo

## 1.3.3 → 1.4.0

`1.4.0` 对齐 fastD 8.2：

```json
"fastd/fastd": "^8.2"
```

### Swoole 配置

- 端口监听器键：`servers.*.listener`
- Swoole 事件监听器：`listeners`
- Swoole 原生配置：`options`

### 安装脚本

安装脚本只修改原配置 `return` 数组中的 demo 注入区：

- 不新增配置文件
- 保留用户已有 server / service 配置
- 重复执行幂等
- Demo 不存在时通过 `class_exists()` 自动降级
