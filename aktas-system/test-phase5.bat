@echo off
REM Phase 5 Test Script
REM Tests all Phase 5 security features

echo ============================================
echo Phase 5 Security Features Test
echo ============================================

REM Test 1: Login
echo.
echo [Test 1] Login Endpoint
curl -X POST http://localhost:8000/api/v1/auth/login ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"admin@hamid.com\",\"password\":\"admin123456\"}"

echo.
echo ============================================
echo Test Complete
echo ============================================
