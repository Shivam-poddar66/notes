@echo off
title Offensive Security Practice Labs
echo ========================================================
echo   OFFENSIVE SECURITY & ETHICAL HACKING PRACTICE LABS
echo ========================================================
echo.
echo Starting local vulnerable web server on http://127.0.0.1:8888 ...
echo Press Ctrl+C in this window to stop the server anytime.
echo.
start http://127.0.0.1:8888
php -S 127.0.0.1:8888
pause
