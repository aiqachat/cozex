#!/bin/bash

# 创建临时容器复制代码到卷
docker run --rm -v cozex_volume:/target -v $(pwd)/../src:/source alpine \
    sh -c "cp -r /source/. /target && chown -R 33:33 /target"

chmod 644 mysql/my.cnf