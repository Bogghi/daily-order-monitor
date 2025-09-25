drop schema if exists iliad;
create schema iliad;
create table iliad.orders (
    order_id int auto_increment primary key,
    customer_name varchar(255) not null,
    order_date datetime default current_timestamp,
    value int not null
);
create table iliad.products (
    product_id int auto_increment primary key,
    name varchar(255) not null,
    description text,
    price decimal(10, 2) not null
);
create table iliad.order_items (
    order_item_id int auto_increment primary key,
    order_id int not null,
    product_id int not null,
    quantity int not null,
    foreign key (order_id) references iliad.orders(order_id),
    foreign key (product_id) references iliad.products(product_id)
);
create table iliad.users (
    user_id int primary key auto_increment,
    username varchar(50) not null unique,
    password_hash varchar(255) not null,
    created_at datetime default current_timestamp
);
create table iliad.users_oauth_tokens (
    user_oauth_token_id int primary key auto_increment,
    user_id int not null,
    token varchar(255) not null unique,
    expires_at datetime not null,
    issued_at datetime default null,
    foreign key (user_id) references users(user_id) on delete cascade
);