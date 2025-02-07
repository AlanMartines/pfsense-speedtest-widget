<h1>Speedtest Dashboard Widget para pfSense</h1>

<p>Este widget foi desenvolvido para adicionar a funcionalidade de <em>speedtest</em> ao pfSense, permitindo a listagem e conexão automática com servidores geograficamente próximos, o que proporciona maior precisão nos resultados dos testes de velocidade de internet.</p>

<h2>Requisitos</h2>

<ul>
  <li>pfSense</li>
  <li>speedtest-cli</li>
  <li>jq</li>
</ul>

<h2>Instalação do <code>speedtest-cli</code></h2>

<p>Execute os seguintes comandos no terminal do seu pfSense para instalar o <code>speedtest-cli</code> e suas dependências:</p>

<pre><code>pkg update
set package_name=`pkg search speedtest-cli | awk '{ print $1 }'`
pkg install -y $package_name
pkg install -y jq
</code></pre>

<h2>Testando o <code>speedtest-cli</code></h2>

<p>Após a instalação, você pode executar o teste de velocidade de forma simples:</p>

<pre><code>speedtest-cli --secure
</code></pre>

<p>Para obter a saída em formato JSON e analisar os dados com <code>jq</code>, use o comando:</p>

<pre><code>speedtest-cli --secure --json | jq
</code></pre>

<h2>Instalando o Widget no pfSense</h2>

<p>Para adicionar o widget ao seu dashboard do pfSense, execute o comando abaixo:</p>

<p><strong>Versão inicial:</strong></p>
<p align="left"><img src="https://github.com/user-attachments/assets/c0c452e2-fbf7-4676-80ea-e5055197f0c7" alt="Speedtest Widget Screenshot"></p>

<pre><code>curl -LJ https://github.com/AlanMartines/pfsense-speedtest-widget/raw/refs/heads/master/speedtest.widget.old.php -o /usr/local/www/widgets/widgets/speedtest.widget.php
</code></pre>

<p><strong>Versão atualizada:</strong></p>
<p align="left"><img src="https://github.com/user-attachments/assets/667cf652-c9f9-463e-a315-53c28ff9e451" alt="Speedtest Widget Screenshot"></p>

<pre><code>curl -LJ https://github.com/AlanMartines/pfsense-speedtest-widget/raw/refs/heads/master/speedtest.widget.php -o /usr/local/www/widgets/widgets/speedtest.widget.php
</code></pre>

<p><strong>Versão atualizada com opção de interfaces:</strong></p>
<p align="left"><img src="https://github.com/user-attachments/assets/e70f288d-477b-4757-a7b2-9d4cc40beea3" alt="Speedtest Widget Screenshot"></p>
<p align="left"><img src="https://github.com/user-attachments/assets/44732861-f17a-4cdf-8450-6b0a08903ba3" alt="Speedtest Widget Screenshot"></p>

<pre><code>curl -LJ https://github.com/AlanMartines/pfsense-speedtest-widget/raw/refs/heads/master/speedtest.widget.int.php -o /usr/local/www/widgets/widgets/speedtest.widget.php
</code></pre>

<h2>Créditos</h2>

<p>Este widget foi criado com base nos seguintes repositórios e tutoriais:</p>

<ul>
  <li><a href="https://github.com/vaamonde/pfsense/blob/main/pfsense-2.6-plus/Etapa-019-AdicionandoWidgetSpeedTest.txt">Etapa-019-AdicionandoWidgetSpeedTest.txt</a></li>
  <li><a href="https://github.com/LeonStraathof/pfsense-speedtest-widget">pfsense-speedtest-widget de LeonStraathof</a></li>
</ul>
