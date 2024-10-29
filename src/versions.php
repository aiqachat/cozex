<?php

return [
    '1.0.0' => function () {
        $sql = <<<EOF
EOF;
        sql_execute($sql);
    },
    '1.0.1' => function () {
        $sql = <<<EOF
alter table wstx_av_data add `data` text DEFAULT null COMMENT '数据，包括配置等等';
EOF;
        sql_execute($sql);
    },
];
