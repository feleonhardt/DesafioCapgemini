create database DesafioCapgemini;
use DesafioCapgemini;
create table anuncios (
id int auto_increment not null primary key,
nomeAnuncio varchar(100) not null,
nomeCliente varchar(100) not null,
dataInicio date not null,
dataFim date not null,
reais double not null
);

insert into anuncios(nomeAnuncio, nomeCliente, dataInicio, dataFim, reais) values('Coca-cola', 'Felipe', '2021-05-15', '2021-05-30', 100);
select * from anuncios;