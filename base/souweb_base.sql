-- SQL Manager Lite for PostgreSQL 6.2.0.54471
-- ---------------------------------------
-- Host      : 192.168.0.114
-- Database  : souweb_base
-- Version   : PostgreSQL 12.5 (Ubuntu 12.5-0ubuntu0.20.04.1) on x86_64-pc-linux-gnu, compiled by gcc (Ubuntu 9.3.0-17ubuntu1~20.04) 9.3.0, 64-bit



--
-- Definition for function bustxt_trigger : 
--
SET search_path = public, pg_catalog;
SET check_function_bodies = false;
CREATE FUNCTION public.bustxt_trigger (
)
RETURNS trigger
AS 
$body$
begin
  new.bustxt_ft :=
    setweight(to_tsvector(coalesce(new.bustxt,'')), 'A');
return new;
end
$body$
LANGUAGE plpgsql;
--
-- Definition for function count_estimate : 
--
CREATE FUNCTION public.count_estimate (
  query text
)
RETURNS integer
AS 
$body$
DECLARE
    rec   record;
    ROWS  INTEGER;
BEGIN
    FOR rec IN EXECUTE 'EXPLAIN ' || query LOOP
        ROWS := SUBSTRING(rec."QUERY PLAN" FROM ' rows=([[:digit:]]+)');
        EXIT WHEN ROWS IS NOT NULL;
    END LOOP;
 
    RETURN ROWS;
END
$body$
LANGUAGE plpgsql;
--
-- Definition for function pagtex_trigger : 
--
CREATE FUNCTION public.pagtex_trigger (
)
RETURNS trigger
AS 
$body$
begin
  new.pagtex_ft :=
    setweight(to_tsvector('portuguese',coalesce(new.pagtex,'')), 'A');
return new;
end
$body$
LANGUAGE plpgsql;
--
-- Definition for function pagtit_trigger : 
--
CREATE FUNCTION public.pagtit_trigger (
)
RETURNS trigger
AS 
$body$
begin
  new.pagtit_ft :=
    setweight(to_tsvector(coalesce(new.pagtit,'')), 'A');
return new;
end
$body$
LANGUAGE plpgsql;
--
-- Structure for table buscas : 
--
CREATE TABLE public.buscas (
    buscod serial NOT NULL,
    bustxt varchar(255),
    bustxt_ft tsvector
)
WITH (oids = false);
--
-- Structure for table dominios : 
--
CREATE TABLE public.dominios (
    domcod serial NOT NULL,
    domnom varchar(255),
    dompag integer,
    dommax integer DEFAULT 100 NOT NULL
)
WITH (oids = false);
--
-- Structure for table midias : 
--
CREATE TABLE public.midias (
    midcod serial NOT NULL,
    pagcod integer,
    midurl varchar(255) NOT NULL
)
WITH (oids = false);
--
-- Structure for table paginas : 
--
CREATE TABLE public.paginas (
    pagcod serial NOT NULL,
    pagurl varchar(500),
    pagtex text,
    pagtit varchar(255),
    pagtit_ft tsvector,
    pagtex_ft tsvector,
    domcod integer,
    pagmid integer,
    padgat date DEFAULT CURRENT_DATE
)
WITH (oids = false);
--
-- Structure for table rel_pag_ara : 
--
CREATE TABLE public.rel_pag_ara (
    rpacod serial NOT NULL,
    pagcod integer,
    rpadat date DEFAULT now() NOT NULL,
    rpaatu date,
    rpasta integer DEFAULT 1 NOT NULL
)
WITH (oids = false);
--
-- Structure for table rel_pag_bus : 
--
CREATE TABLE public.rel_pag_bus (
    rpbcod serial NOT NULL,
    buscod integer,
    pagcod integer,
    rpbpon integer DEFAULT 0,
    rpbsim integer DEFAULT 0,
    rpbnao integer DEFAULT 0
)
WITH (oids = false);
--
-- Structure for table temporaria : 
--
CREATE TABLE public.temporaria (
    tmpcod serial NOT NULL,
    tmpurl varchar(500),
    tmplid boolean
)
WITH (oids = false);
--
-- Definition for index bustxt_ft_idx : 
--
CREATE INDEX bustxt_ft_idx ON public.buscas USING gin (bustxt_ft);
--
-- Definition for index pagtex_ft_idx : 
--
CREATE INDEX pagtex_ft_idx ON public.paginas USING gin (pagtex_ft);
--
-- Definition for index pagtit_ft_idx : 
--
CREATE INDEX pagtit_ft_idx ON public.paginas USING gin (pagtit_ft);
--
-- Definition for index buscas_pkey : 
--
ALTER TABLE ONLY buscas
    ADD CONSTRAINT buscas_pkey
    PRIMARY KEY (buscod);
--
-- Definition for index dominios_pkey : 
--
ALTER TABLE ONLY dominios
    ADD CONSTRAINT dominios_pkey
    PRIMARY KEY (domcod);
--
-- Definition for index midia_midurl_key : 
--
ALTER TABLE ONLY midias
    ADD CONSTRAINT midia_midurl_key
    UNIQUE (midurl);
--
-- Definition for index paginas_pkey : 
--
ALTER TABLE ONLY paginas
    ADD CONSTRAINT paginas_pkey
    PRIMARY KEY (pagcod);
--
-- Definition for index pagurl_uni : 
--
ALTER TABLE ONLY paginas
    ADD CONSTRAINT pagurl_uni
    UNIQUE (pagurl);
--
-- Definition for index rel_pag_bus_pkey : 
--
ALTER TABLE ONLY rel_pag_bus
    ADD CONSTRAINT rel_pag_bus_pkey
    PRIMARY KEY (rpbcod);
--
-- Definition for index temporaria_pkey : 
--
ALTER TABLE ONLY temporaria
    ADD CONSTRAINT temporaria_pkey
    PRIMARY KEY (tmpcod);
--
-- Definition for index tmpurl_uni : 
--
ALTER TABLE ONLY temporaria
    ADD CONSTRAINT tmpurl_uni
    UNIQUE (tmpurl);
--
-- Definition for index midias_fk : 
--
ALTER TABLE ONLY midias
    ADD CONSTRAINT midias_fk
    FOREIGN KEY (pagcod) REFERENCES paginas(pagcod) ON UPDATE CASCADE ON DELETE CASCADE;
--
-- Definition for index pag_dom_fkey : 
--
ALTER TABLE ONLY paginas
    ADD CONSTRAINT pag_dom_fkey
    FOREIGN KEY (domcod) REFERENCES dominios(domcod) ON UPDATE CASCADE ON DELETE CASCADE;
--
-- Definition for index rel_pag_ara_fk : 
--
ALTER TABLE ONLY rel_pag_ara
    ADD CONSTRAINT rel_pag_ara_fk
    FOREIGN KEY (pagcod) REFERENCES paginas(pagcod) ON UPDATE CASCADE ON DELETE CASCADE;
--
-- Definition for index rpb_bus_fkey : 
--
ALTER TABLE ONLY rel_pag_bus
    ADD CONSTRAINT rpb_bus_fkey
    FOREIGN KEY (buscod) REFERENCES buscas(buscod) ON UPDATE CASCADE ON DELETE CASCADE;
--
-- Definition for index rpb_pag_fkey : 
--
ALTER TABLE ONLY rel_pag_bus
    ADD CONSTRAINT rpb_pag_fkey
    FOREIGN KEY (pagcod) REFERENCES paginas(pagcod) ON UPDATE CASCADE ON DELETE CASCADE;
--
-- Definition for trigger bustxt_trigger_update : 
--
CREATE TRIGGER bustxt_trigger_update
    BEFORE INSERT OR UPDATE OF bustxt ON buscas
    FOR EACH ROW
    EXECUTE PROCEDURE bustxt_trigger ();
--
-- Definition for trigger pagtex_trigger_update : 
--
CREATE TRIGGER pagtex_trigger_update
    BEFORE INSERT OR UPDATE OF pagtex ON paginas
    FOR EACH ROW
    EXECUTE PROCEDURE pagtex_trigger ();
--
-- Definition for trigger pagtit_trigger_update : 
--
CREATE TRIGGER pagtit_trigger_update
    BEFORE INSERT OR UPDATE OF pagtit ON paginas
    FOR EACH ROW
    EXECUTE PROCEDURE pagtit_trigger ();
--
-- Comments
--
COMMENT ON SCHEMA public IS 'standard public schema';
COMMENT ON COLUMN public.paginas.pagmid IS '0 - página, 1 - áudio, 2 - vídeo';
COMMENT ON COLUMN public.rel_pag_ara.rpasta IS '0.Offline 1.Online 2.Rastrear Novamente';
