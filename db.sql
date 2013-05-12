create table ADDRESS (
	ADDRESSID int not null auto_increment primary key comment 'The unique address ID.',
	LABEL varchar(100) not null comment 'The name of the person or organisation to which the address belongs.',
	STREET varchar(100) not null comment 'The name of the street.',
	HOUSENUMBER varchar(10) not null comment 'The house number (and any optional additions).',
	POSTALCODE varchar(6) not null comment 'The postal code for the address.',
	CITY varchar(100) not null comment 'The city in which the address is located.',
	COUNTRY varchar(100) not null comment 'The country in which the address is located.'
)
engine 'InnoDB',
character set utf8,
collate utf8_general_ci,
comment 'A physical address belonging to a person or organisation.';
