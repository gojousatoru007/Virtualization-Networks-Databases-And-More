
# Networks-Servers-Databases-And-More

***

We have to Create 3 Virtual Machines using any Linux Version with any Hypervisor.

We have to Install any Web Server on VM1, Any DataBase on VM2 and then create a dummy web application to insert a data record in the database and display it.

Demonstrate that the web application is accessible from VM3 and the host system web browser.

Here is a YouTube Video for reference: [Virtualization](https://youtu.be/p3O1ZkDj-kw)
***

## Network Configuration of VMs

### VM1:
Network Configuration of VM1 can be observed from the Screenshots of VMWare's Settings of the Virtual Machine

We have used Bridged Network Adapter as one of one of our Network Adapter, and we check the option to Replicate Physical System.
This is important, as Bridged Mode replicates another node as a physical network and our VM will receive our own IP Address if DHCP is enabled in the network. This helps us to use the Network adapter as a different entity (more like something a standalone OS would offer)

Now, we repeat the same Network Configurations for other Virtual Machines.

VM2 should be able to listen to incoming traffic on it's IP Address as it hosts the database!

VM3 should be able to ping atleast VM1's and if possible VM2's IP, just to make sure that these machines can communicate to each other!

The WebApplication is hosted on the Server in VM1.


**VM1** is the Virtual Machine in which we have installed our Web Server.
We have used APACHE as our webserver.

For Arch:
```bash
sudo pacman -S apache
```
Will install apache webserver in VM1

```bash
sudo systemctl enable httpd.service

sudo systemctl start httpd.service

sudo systemctl status httpd.service
```

### Some configuration for APACHE
```bash
sudo vim /etc/httpd/conf/httpd.conf
```
Search for the module "unique_id_module" and enable it by uncommenting the line and save the file
Reload the server
```bash
sudo systemctl restart httpd
```
this will provide unique token for each request which guarantees to be unique under all conditions.

Now, we comment the "mpm_event_module" and uncomment the prefork module in
```bash
sudo vim /etc/httpd/conf/httpd.conf
```

also go down to the end of the file and add the following lines
```bash
LoadModule php_module modules/libphp.so
AddHandler php-script php
Include conf/extra/php_module.conf
```
helps the server with child processes!
Further, we have to configure our php.ini file to enable some extensions there

```bash
sudo vim /etc/php/php.ini
```
search for mysqli extension and enable it by uncomment it and do the same for the gd extension!

save the file and exit vim!

php should be working now!
### Installing and Configuring a database server on VM_B21AI049_2

Here we have used PostgreSQL as our database
```bash
sudo pacman -S postgresql
sudo systemctl enable postgresql.service
sudo systemctl start postgresql.service
sudo passwd postgres
```



further we login to the postgres user using the following command:

```bash
sudo -i -u postgres
psql
```

Now we use php to program server, first we install it.

```bash
sudo pacman -S php php-apache php-pgsql
```
```bash
sudo systemctl restart httpd.service
```


## Steps to create a Database and a table to store user inputs from our webapplciation



1. Log in to PostgreSQL as the postgres user:
```bash
sudo -i -u postgres
psql
```


2. Create a new database:
```bash
CREATE DATABASE myapp;
```

3. Create a new user and set a password:
```bash
CREATE USER myuser WITH PASSWORD 'mypassword';
```

4. Grant Privileges to the new user on the new database:
```bash
GRANT ALL PRIVILEGES ON DATABASE myapp TO myuser;
```

5. Connect to the 'myapp' database:
```bash
\c myapp;
```

6. Create a new table:
```bash
CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  name VARCHAR(50),
  age INT,
  email VARCHAR(50),
  password VARCHAR(50)
);
```

7. To see what's inside the table:
```bash
SELECT * FROM users;
```

__Note:__ In order to allow other users or instances of webapp to add and delete stuff from this table we need to GRANT access as well:
```bash
GRANT USAGE, SELECT, UPDATE ON SEQUENCE users_id_seq TO myuser;
```  

## Editing the postgresql.conf file
We need to be able to access this database server from other VM's as well so we have to edit the conf file

```bash
sudo nano /var/lib/postgres/data/postgresql.conf
```

Look for 'listen_addresses' settings and change it to:
```bash
listen_addresses = '*'
```
Save the file and then edit 'pg_hba.conf' file next:

```bash
sudo nano /var/lib/postgres/data/pg_hba.conf
```
Add the following line at the end of the file:
```bash
host    all             all             <IP address of VM_RollNumber_1>/32        md5
```

Now, restart the postgresql service and we are ready to go!
***
***
## Creating Webpage

We place our 'index.php' file in /srv/http

```bash
sudo nano /srv/http/index.php
```




I will attach the php file for your kind reference with other relevant screenshots and material!


## Allowing HTTP traffic on port 80

To make sure that incoming traffic is allowed to our web server and the database server, we can check the firewall rules on our VMs using the following command:

```bash
sudo iptables -L
```

Make sure that there is a rule allowing incoming traffic on port 80 (HTTP) and port 443 (HTTPS) if you're using HTTPS. If there is no such rule, you can add it using the following command:
```bash
sudo iptables -A INPUT -p tcp --dport 80 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 443 -j ACCEPT
```
save the changes by:
```bash
sudo service iptables save
```

else we could also just do

```bash
sudo ufw allow 80/tcp
```
or
```bash
sudo ufw allow 80
```
