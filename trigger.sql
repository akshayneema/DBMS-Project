-- create or replace function check_availability(p_id integer, check_in_date timestamp, check_out_date timestamp) 
-- returns boolean
-- as 
-- $$  
-- begin
--     if((select count(*) from calender where listing_id=p_id and date>=check_in_date and date<=check_in_date and available="f")>0) then
--         return 'f';
--     else
--         return 't';
--     end if;
-- commit; 
-- end;
--  $$ language 'plpgsql';


-- CREATE TRIGGER book AFTER INSERT ON bookings
-- REFERENCING
-- NEW ROW AS NewTuple,
-- NEW TABLE AS NewStuff
-- FOR FEXECUTE PROCEDURE function_name()

CREATE OR REPLACE FUNCTION check_availability()
  RETURNS trigger AS
$$
BEGIN
    if((select count(*) from calender where listing_id=new.property_id and date>=new.check_in_date and date<=new.check_in_date and available='f')>0) then
        INSERT INTO bookings(BOOKING_ID);
    else
        select 't';
    end if;
 
   RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER test
AFTER INSERT
ON bookings
FOR EACH ROW 
EXECUTE PROCEDURE log_last_name_changes();;

