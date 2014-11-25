GloryFrame
================
个人框架


目录结构

application         应用目录
    -                   结构参考源码目录
builds              编译目录
    backup              站点备份目录
    files               站点上传目录
    sites               站点配置目录
    versions            站点编译版本
        7.x-1.0
        7.x-1.1
    web                 站点目录，链接到编译版本
scripts             脚本目录
    setting.sh          通用脚本，包括函数、变量、初始化
    release.sh          发布脚本
    backup.sh           备份脚本
    update.sh           升级脚本
sources             源码目录
    configs             配置
    libraries           程序库
    modules             模块
    themes              主题