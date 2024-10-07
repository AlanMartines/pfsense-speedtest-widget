# Speedtest dashboard widget para pfSense

O widget foi desenvolvido para adicionar a funcionalidade de speedtest ao pfSense, permitindo a listagem e conexão automática com servidores geograficamente próximos, garantindo maior precisão nos resultados.

# Instalação `speedtest-cli`
```sh
pkg update;
set package_name = `pkg search speedtest-cli | awk '{ print $1 }'`;
pkg install -y $package_name;
pkg install -y jq;
```

# Teste `speedtest-cli`
```sh
speedtest-cli --secure
```
```sh
speedtest-cli --secure --json | jq
```

# Instalando o Widget
```sh
curl -LJ https://raw.githubusercontent.com/AlanMartines/pfsense-speedtest-widget/speedtest.widget.php -o /usr/local/www/widgets/widgets/speedtest.widget.php
```
