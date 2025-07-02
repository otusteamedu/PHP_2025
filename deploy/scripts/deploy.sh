#!/bin/bash

# Параметры деплоя
ENVIRONMENT=${1:-production}
SERVER_IP="your_server_ip"
DEPLOY_USER="deploy"

echo "Начинаем деплой в окружение $ENVIRONMENT..."

# Копируем конфиги и Ansible плейбуки
rsync -avz -e "ssh -o StrictHostKeyChecking=no" \
  ./deploy/ $DEPLOY_USER@$SERVER_IP:/tmp/fastfood-deploy/

# Запускаем Ansible плейбук
ssh -o StrictHostKeyChecking=no $DEPLOY_USER@$SERVER_IP << EOF
  cd /tmp/fastfood-deploy/ansible
  ansible-playbook -i inventory.ini playbook.yml --extra-vars "env=production"
EOF

echo "Деплой завершен успешно!"