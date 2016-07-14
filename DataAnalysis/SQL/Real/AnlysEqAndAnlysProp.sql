CREATE TABLE anlys_equipment(
	anlys_eq_ID INT AUTO_INCREMENT,
    anlys_eq_name VARCHAR(50),
    anlys_eq_comment VARCHAR(2000),
    anlys_eq_active BOOLEAN,
    PRIMARY KEY(anlys_eq_ID)
);

INSERT INTO anlys_equipment(anlys_eq_name, anlys_eq_active) VALUES
		("Dektak",TRUE), ("Rockwell Hardness Tester", TRUE), ("LaWave", TRUE),
		("Contact Angle Goniometer", TRUE), ("Tribometer", TRUE), ("UV VIS", TRUE),
		("Calotte Grinder", TRUE), ("AFM", TRUE), ("Stereo Microscope", TRUE),
		("Nikon Microscope", TRUE), ("Laurane", TRUE), ("XPS", TRUE);
            
CREATE TABLE anlys_property(
		anlys_prop_ID INT AUTO_INCREMENT,
        anlys_prop_name VARCHAR(50),
        PRIMARY KEY(anlys_prop_ID),
        CONSTRAINT uniq_prop_name UNIQUE (anlys_prop_name)
);

-- ALTER TABLE anlys_property
-- ADD CONSTRAINT uniq_prop_name UNIQUE (anlys_prop_name);

INSERT INTO anlys_property(anlys_prop_name) VALUES
			("Thickness"), ("Roughness"), ("Color"), ("Adhesion"), 
            ("Young's Modulus"), ("Contact angle"), ("Wear rate"),
            ("Coefficient of friction"), ("Transparency"), ("Reflectance"),
            ("Density"), ("Atomic composition");

-- anlys_param_X are names of extra parameters e.g. angle when measuring thickness. --
CREATE TABLE anlys_eq_prop(
		anlys_eq_prop_ID INT AUTO_INCREMENT,
        anlys_eq_ID INT,
        anlys_prop_ID INT,
        anlys_param_1 VARCHAR(50),
        anlys_param_2 VARCHAR(50),
        anlys_param_3 VARCHAR(50),
        PRIMARY KEY(anlys_eq_prop_ID),
        FOREIGN KEY(anlys_eq_ID) REFERENCES anlys_equipment(anlys_eq_ID),
        FOREIGN KEY(anlys_prop_ID) REFERENCES anlys_property(anlys_prop_ID),
        CONSTRAINT uniq_eq_prop UNIQUE (anlys_eq_ID, anlys_prop_ID)
);

-- ALTER TABLE anlys_eq_prop
-- ADD CONSTRAINT uniq_eq_prop UNIQUE (anlys_eq_ID, anlys_prop_ID);

INSERT INTO anlys_eq_prop(anlys_eq_ID, anlys_prop_ID) VALUES
		(1,2),(8,2),(1,1),(11,1),(7,1),
        (6,1),(9,3),(10,3),(2,4),(3,5),
        (4,6),(5,7),(5,8),(6,9),(6,10),
        (11,11),(12,12);

-- Add parameters to the Calotte grinder. 
UPDATE anlys_eq_prop
SET anlys_param_1 = 'Revolutions', anlys_param_2 = 'Angle'
WHERE anlys_eq_prop_ID = 5;

-- INSERT INTO anlys_property(anlys_prop_name, anlys_eq_ID) VALUES
-- 			("Roughness", 1), ("Roughness", 8), ("Thickness", 1), ("Thickness", 11),
--             ("Thickness", 7), ("Thickness", 6), ("Color", 9), ("Color", 10),
-- 			("Adhesion", 2), ("Young's Modulus", 3), ("Contact angle", 4),
--             ("Wear rate", 5), ("Coefficient of friction", 5), ("Transparency", 6),
--             ("Reflectance", 6), ("Density", 11), ("Atomic composition", 12);

CREATE TABLE anlys_result(
	anlys_res_ID INT AUTO_INCREMENT,
    anlys_res_result DOUBLE,
    anlys_res_comment VARCHAR(2000),
    anlys_file MEDIUMBLOB,
    anlys_res_1 DOUBLE,	-- Result of anlys_param_1 --
    anlys_res_2 DOUBLE, -- Result of anlys_param_2 --
    anlys_res_3 DOUBLE, -- Result of anlys_param_3 --
    PRIMARY KEY(anlys_res_ID)
);

CREATE TABLE anlys_average(
	anlys_aveg_ID INT AUTO_INCREMENT,
    anlys_aveg_result DOUBLE,
    anlys_res_ID_1 INT,
    anlys_res_ID_2 INT,
    anlys_res_ID_3 INT,
    anlys_res_ID_4 INT,
    anlys_res_ID_5 INT,
    PRIMARY KEY(anlys_aveg_ID),
    FOREIGN KEY(anlys_res_ID_1) REFERENCES anlys_result(anlys_res_ID),
    FOREIGN KEY(anlys_res_ID_2) REFERENCES anlys_result(anlys_res_ID),
    FOREIGN KEY(anlys_res_ID_3) REFERENCES anlys_result(anlys_res_ID),
    FOREIGN KEY(anlys_res_ID_4) REFERENCES anlys_result(anlys_res_ID),
    FOREIGN KEY(anlys_res_ID_5) REFERENCES anlys_result(anlys_res_ID)
);

CREATE TABLE anlalysis(
	anlys_ID INT AUTO_INCREMENT,
    sample_ID INT,
    anlys_aveg_ID INT,
    PRIMARY KEY(anlys_ID),
    FOREIGN KEY(sample_ID) REFERENCES sample(sample_ID),
    FOREIGN KEY(anlys_aveg_ID) REFERENCES anlys_average(anlys_aveg_ID)
);
    
