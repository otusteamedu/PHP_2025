--
Create or replace function random_string(length integer) returns text as
$$
declare
chars text[] := '{0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z}';
    result text := '';
    i integer := 0;
begin
    if length < 0 then
        raise exception 'Given length cannot be less than 0!';
end if;
for i in 1..length loop
        result := result || chars[1 + random() * (array_length(chars, 1) - 1)];
end loop;
return result;
end;
$$ language plpgsql;

set my.max_chars_varying_20 = 20;
set my.max_chars_varying_50 = 50;
set my.max_chars_varying_100 = 100;

set my.amount_data_to_generate_smallserial = 32; -- 32000
set my.amount_data_to_generate_serial = 10000; -- 10000000
set my.amount_data_to_generate_bigserial = 10000; -- 10000000

insert into cinema.movie(name)
    select
        random_string(1 + floor(random() * current_setting('my.max_chars_varying_100')::int)::int)
    from generate_series(1,current_setting('my.amount_data_to_generate_bigserial')::int) as gs(id);

insert into cinema.cinema(city, address)
    select
        random_string(1 + floor(random() * current_setting('my.max_chars_varying_50')::int)::int),
        random_string(1 + floor(random() * 200))
    from generate_series(1,current_setting('my.amount_data_to_generate_smallserial')::int) as gs(id);

insert into cinema.customer(name, email, phone)
    select
        random_string(1 + floor(random() * current_setting('my.max_chars_varying_50')::int)::int),
        random_string(1 + floor(random() * current_setting('my.max_chars_varying_50')::int)::int),
        random_string(1 + floor(random() * current_setting('my.max_chars_varying_20')::int)::int)
    from generate_series(1, current_setting('my.amount_data_to_generate_bigserial')::int) as gs(id);