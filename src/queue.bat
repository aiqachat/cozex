@echo off

cd %~dp0
set PROJECT_PATH=%~dp0
set KEYWORD="%PROJECT_PATH%yii" queue/listen

rem @echo %KEYWORD%

@echo 正在检查进程...
wmic process where name="php.exe" get CommandLine /value | findstr "%KEYWORD%" >nul
if %errorlevel% equ 0 (
goto end
) else (
goto start
)

:start
@echo 正在启动进程...
start /b yii queue/listen 1
@echo 进程已启动，请勿关闭本窗口！
goto end

:end
