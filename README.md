<h1>Speedtest Dashboard Widget para pfSense</h1>

<p>Este widget foi desenvolvido para adicionar a funcionalidade de <em>speedtest</em> ao pfSense, permitindo a listagem e conexão automática com servidores geograficamente próximos, o que proporciona maior precisão nos resultados dos testes de velocidade de internet.</p>

<h2>Requisitos</h2>

<ul>
  <li>pfSense</li>
  <li><code>speedtest-cli</code></li>
  <li><code>jq</code></li>
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

<pre><code>curl -LJ https://github.com/AlanMartines/pfsense-speedtest-widget/raw/refs/heads/master/speedtest.widget.php -o /usr/local/www/widgets/widgets/speedtest.widget.php
</code></pre>

<h3>Exemplo de Tela do Widget</h3>

<p><center><img src="https://github.com/user-attachments/assets/8aefa6a0-2f9c-4bd9-9bcb-b5d62169c6e0" alt="Speedtest Widget Screenshot"></center></p>

<h2>Créditos</h2>

<p>Este widget foi criado com base nos seguintes repositórios e tutoriais:</p>

<ul>
  <li><a href="https://github.com/vaamonde/pfsense/blob/main/pfsense-2.6-plus/Etapa-019-AdicionandoWidgetSpeedTest.txt">Etapa-019-AdicionandoWidgetSpeedTest.txt</a></li>
  <li><a href="https://github.com/LeonStraathof/pfsense-speedtest-widget">pfsense-speedtest-widget de LeonStraathof</a></li>
</ul>
