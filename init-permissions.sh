# Sistema	                 Funciona?	      Observação
# Ubuntu	                 ✅ Sim	        100% compatível
# Debian	                 ✅ Sim	        100% compatível
# CentOS 7	                 ✅ Sim	        100% compatível | instalar usermod / coreutils se não tiver
# WSL2 (Ubuntu)	             ✅ Sim	        100% compatível
# Windows puro (sem WSL)	 ❌ Não	        UID/GID e usermod não existem no Windows


echo 'Ajustando permissões...'

# Adiciona seu usuário ao grupo www-data (usado pelo Apache no container)
sudo usermod -aG www-data $USER

# Muda o dono dos arquivos: você como dono, www-data como grupo
sudo chown -R $USER:www-data .

# Dá permissão de leitura e escrita para dono e grupo
sudo chmod -R 775 .

echo 'Permissões ajustadas, agora rode - docker-compose up -d'

# Atualiza o grupo atual da sessão sem precisar reiniciar
newgrp www-data
