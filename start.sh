#!/bin/bash

# ----------------------------------------------------------------------
# 启动 crontab
# ----------------------------------------------------------------------
crond

# ----------------------------------------------------------------------
# 启动 websocket 接口
# ----------------------------------------------------------------------
exec /usr/bin/php /var/api/public/api.php start -d