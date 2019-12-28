@echo off

REM change cmd weight and height lines=... %
REM mode con: cols=100
REM mode 800
mode con: cols=100


%~dp0php-5.6.16-nts-Win32-VC11-x86\php.exe sic.php

pause
echo.

%~dpnx0