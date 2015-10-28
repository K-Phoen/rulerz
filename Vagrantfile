Vagrant.configure(2) do |config|

    config.vm.box = "ubuntu/trusty64"

    config.vm.provision :shell, :path => "vagrant/bootstrap.sh"

    config.vm.provider "virtualbox" do |v|
        v.customize ["modifyvm", :id, "--memory", 1024]
    end

end
