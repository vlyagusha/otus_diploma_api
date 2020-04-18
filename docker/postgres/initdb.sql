create extension intarray;

create user otus with password 'otus';

create database otus with owner = otus;

create or replace function random_string(length int) returns text as
$$
declare
    chars text[] := '{0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z}';
    result text := '';
    i int := 0;
begin
    if length < 0 then
        raise exception 'Given length cannot be less than 0';
    end if;
    for i in 1..length loop
        result := result || chars[1+random()*(array_length(chars, 1)-1)];
    end loop;
    return result;
end;
$$ language plpgsql;

create or replace function random_int(max int) returns int as
$$
begin
    if max < 1 then
        raise exception 'Given max cannot be less than 1';
    end if;

    return (1 + random() * (max - 1))::int;
end;
$$ language plpgsql;

create or replace function random_int_array(max_array_length int, max_int int) returns int[] as
$$
declare
    array_length int := 0;
    result int[] := '{}';
    i int := 0;
begin
    if max_int < 1 then
        raise exception 'Given max_int cannot be less than 1';
    end if;

    if max_array_length < 1 then
        raise exception 'Given max_array_length cannot be less than 1';
    end if;

    array_length := random_int(max_array_length);
    for i in 1..array_length loop
        result := result || random_int(max_int);
    end loop;
    return result;
end;
$$ language plpgsql;

-- insert into user_movie_preferences(user_id, movies)
-- select random_string(8), random_int_array(10, 100) from generate_series(1, 100000);
