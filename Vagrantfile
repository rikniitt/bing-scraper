# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure(2) do |config|
  
  config.vm.box = "ubuntu/trusty64"
  config.vm.hostname = "bing-scraper"

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  # config.vm.network "forwarded_port", guest: 80, host: 8080

  config.vm.provider "virtualbox" do |vb|
    vb.memory = "2048"
  end
  
  config.vm.provision "shell", inline: <<-SHELL
    ## Update and install packages as root
    apt-get update > /dev/null 2>&1
    apt-get install -y php5-cli sqlite php5-sqlite php5-curl git curl tree
  SHELL
  config.vm.provision "shell", privileged: false, inline: <<-SHELL
    ## Install project
    cd /vagrant
    curl -sS https://getcomposer.org/installer | php
    php composer.phar install --no-progress --no-suggest --no-interaction --no-ansi
    cp ./config/config.file.example ./config/config.file
    ./bin/bing-scraper install
  SHELL
end
