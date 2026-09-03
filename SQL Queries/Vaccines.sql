create database Vaccination

use Vaccination

Create table Patients(
Patient_ID int primary key identity(1,1),
Patient_FName Varchar(50) not null,
Patient_LName Varchar(50) not null,
Patient_Username Varchar(50) not null unique,
Patient_Password Varchar(50) not null , 
Patient_City_ID int Foreign Key References Cities(City_ID),
Patient_Phone Varchar(15) not null,
Patient_National_ID  Varchar(14) unique
)

Create Table Vaccines(
Vaccine_ID int Primary key Identity(1,1),
Vaccine_Name Varchar(50) not null,
dose_gap_days int not null,
Precautions Text
)

Create Table Vaccination_Centers(
Center_ID int primary key identity(1,1),
Center_Name Varchar(255) not null,
Center_City_ID int Foreign Key References Cities(City_ID),
Center_Address Text,
Center_Contact_No Varchar(15),
Center_Username Varchar(50) not null unique, 
Center_Password Varchar(50) not null,
)

Create Table Cities(
City_ID int primary key Identity(1,1),
City_Name Varchar(25)
)

Create Table Reservations(
Reservation_ID int Primary Key Identity(1,1),
Patient_ID int Foreign Key References Patients(Patient_ID),
Vaccine_ID int Foreign Key References Vaccines(Vaccine_ID),
Center_ID int Foreign Key References Vaccination_Centers(Center_ID),
Dose_Number int not null,
Scheduled_Date Date,
[1st_Confirmation] TinyInt,
[1st_Confirmation_Date] Date ,
[2nd_Confirmation] TinyInt ,
[2nd_Confirmation_Date] Date 
)

Create Table Admin(
Admin_ID int Primary key Identity(1,1),
Admin_Fname Varchar(15) not null,
Admin_Lname Varchar(15) not null,
Admin_Username Varchar(50) not null unique,
Admin_Password Varchar(50) not null
)


Alter table Patients 
Alter Column Patient_City int not null


INSERT INTO Cities (City_Name) VALUES
('Cairo'),
('Alexandria'),
('Giza'),
('Shubra El-Kheima'),
('Port Said'),
('Suez'),
('Luxor'),
('Aswan'),
('Mansoura'),
('Tanta'),
('Ismailia'),
('Fayoum'),
('Zagazig'),
('Damietta'),
('Minya'),
('Beni Suef'),
('Qena'),
('Sohag'),
('Hurghada'),
('Sharm El-Sheikh');

INSERT INTO Admin (Admin_Fname, Admin_Lname, Admin_Username, Admin_Password)
VALUES ('Ahmed', 'Hassan', '...@gmail.com', '123456789');


INSERT INTO Vaccination_Centers 
    (Center_Name, Center_City_ID, Center_Address, Center_Contact_No, Center_Username, Center_Password)
VALUES 
    ('Nasr City Health Center', 1, '123 Salah Salem Street, Nasr City', '01012345678', '...@gmail.com', '123456789');


