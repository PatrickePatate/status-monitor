SELECT 'CREATE DATABASE status_monitor_testing'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'status_monitor_testing')\gexec
