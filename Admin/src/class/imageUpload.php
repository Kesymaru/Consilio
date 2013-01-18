<?php

require_once("usuarios.php"); 
require_once("proyectos.php"); 

/**
 * This class allows a user to upload and 
 * validate their files.
 *
 * @author John Ciacia <Sidewinder@extreme-hq.com>
 * @version 1.0
 * @copyright Copyright (c) 2007, John Ciacia
 * @license http://opensource.or...gpl-license.php GNU Public License
 */

class Upload {
        
        /**
         *@var string contains the name of the file to be uploaded.
         */
        private $FileName;
        /**
         *@var string contains the temporary name of the file to be uploaded.
         */
        private $TempFileName;
        /**
         *@var string contains directory where the files should be uploaded.
         */
        private $UploadDirectory;
        /**
         *@var string contains an array of valid extensions which are allowed to be uploaded.
         */
        private $ValidExtensions;
        /**
         *@var string contains a message which can be used for debugging.
         */
        private $Message;
        /**
         *@var integer contains maximum size of fiels to be uploaded in bytes.
         */
        private $MaximumFileSize;
        /**
         *@var bool contains whether or not the files being uploaded are images.
         */
        private $IsImage;
        /**
         *@var string contains the email address of the recipient of upload logs.
         */
        private $Email;
        /**
         *@var integer contains maximum width of images to be uploaded.
         */
        private $MaximumWidth;
        /**
         *@var integer contains maximum height of images to be uploaded.
         */
        private $MaximumHeight;

        function Upload()
        {

        }

        /**
         *@method bool ValidateExtension() returns whether the extension of file to be uploaded
         *      is allowable or not.
         *@return true the extension is valid.
         *@return false the extension is invalid.
         */
        function ValidateExtension()
        {

                $FileName = trim($this->FileName);
                $FileParts = pathinfo($FileName);
                $Extension = strtolower($FileParts['extension']);
                $ValidExtensions = $this->ValidExtensions;

                if (!$FileName) {
                        $this->SetMessage("ERROR: File name is empty.");
                        return false;
                }

                if (!$ValidExtensions) {
                        $this->SetMessage("WARNING: All extensions are valid.");
                        return true;
                }

                if (in_array($Extension, $ValidExtensions)) {
                        $this->SetMessage("MESSAGE: The extension '$Extension' appears to be valid.");
                        return true;
                } else {
                        $this->SetMessage("Error: The extension '$Extension' is invalid.");
                        return false;  
                }

        }

        /**
         *@method bool ValidateSize() returns whether the file size is acceptable.
         *@return true the size is smaller than the alloted value.
         *@return false the size is larger than the alloted value.
         */
        function ValidateSize()
        {
                $MaximumFileSize = $this->MaximumFileSize;
                $TempFileName = $this->GetTempName();
                $TempFileSize = filesize($TempFileName);

                if($MaximumFileSize == "") {
                        $this->SetMessage("WARNING: There is no size restriction.");
                        return true;
                }

                if ($MaximumFileSize <= $TempFileSize) {
                        $this->SetMessage("ERROR: The file is too big. It must be less than $MaximumFileSize and it is $TempFileSize.");
                        return false;
                }

                $this->SetMessage("Message: The file size is less than the MaximumFileSize.");
                return true;
        }

        /**
         *@method bool ValidateExistance() determins whether the file already exists. If so, rename $FileName.
         *@return true can never be returned as all file names must be unique.
         *@return false the file name does not exist.
         */
        function ValidateExistance()
        {
                $FileName = $this->FileName;
                $UploadDirectory = $this->UploadDirectory;
                $File = $UploadDirectory . $FileName;

                if (file_exists($File)) {
                        $this->SetMessage("Message: The file '$FileName' already exist.");
                        $UniqueName = rand() . $FileName;
                        $this->SetFileName($UniqueName);
                        $this->ValidateExistance();
                } else {
                        $this->SetMessage("Message: The file name '$FileName' does not exist.");
                        return false;
                }
        }

        /**
         *@method bool ValidateDirectory()
         *@return true the UploadDirectory exists, writable, and has a traling slash.
         *@return false the directory was never set, does not exist, or is not writable.
         */
        function ValidateDirectory()
        {
                $UploadDirectory = $this->UploadDirectory;

                if (!$UploadDirectory) {
                        $this->SetMessage("ERROR: El directorio esta vacio.");
                        return false;
                }

                if (!is_dir($UploadDirectory)) {
                        $this->SetMessage("ERROR: El directorio '$UploadDirectory' no existe.");
                        return false;
                }

                if (!is_writable($UploadDirectory)) {
                        $this->SetMessage("ERROR: El directorio '$UploadDirectory' no es escribible.");
                        return false;
                }

                if (substr($UploadDirectory, -1) != "/") {
                        $this->SetMessage("ERROR: The traling slash does not exist.");
                        $NewDirectory = $UploadDirectory . "/";
                        $this->SetUploadDirectory($NewDirectory);
                        $this->ValidateDirectory();
                } else {
                        $this->SetMessage("MESSAGE: The traling slash exist.");
                        return true;
                }
        }

        /**
         *@method bool ValidateImage()
         *@return true the image is smaller than the alloted dimensions.
         *@return false the width and/or height is larger then the alloted dimensions.
         */
        function ValidateImage() {
                $MaximumWidth = $this->MaximumWidth;
                $MaximumHeight = $this->MaximumHeight;
                $TempFileName = $this->TempFileName;

        if($Size = @getimagesize($TempFileName)) {
                $Width = $Size[0];   //$Width is the width in pixels of the image uploaded to the server.
                $Height = $Size[1];  //$Height is the height in pixels of the image uploaded to the server.
        }

                if ($Width > $MaximumWidth) {
                        $this->SetMessage("The width of the image [$Width] exceeds the maximum amount [$MaximumWidth].");
                        return false;
                }

                if ($Height > $MaximumHeight) {
                        $this->SetMessage("The height of the image [$Height] exceeds the maximum amount [$MaximumHeight].");
                        return false;
                }

                $this->SetMessage("The image height [$Height] and width [$Width] are within their limitations.");        
                return true;
        }

        /**
         *@method bool SendMail() sends an email log to the administrator
         *@return true the email was sent.
         *@return false never.
         *@todo create a more information-friendly log.
         */
        function SendMail() {
                $To = $this->Email;
                $Subject = "File Uploaded";
                $From = "From: Uploader";
                $Message = "A file has been uploaded.";
                mail($To, $Subject, $Message, $From);
                return true;
        }


        /**
         *@method bool UploadFile() uploads the file to the server after passing all the validations.
         *@return true the file was uploaded.
         *@return false the upload failed.
         */
        function UploadFile()
        {

                if (!$this->ValidateExtension()) {
                        die($this->GetMessage());
                } 

                else if (!$this->ValidateSize()) {
                        die($this->GetMessage());
                }

                else if ($this->ValidateExistance()) {
                        die($this->GetMessage());
                }

                else if (!$this->ValidateDirectory()) {
                        die($this->GetMessage());
                }

                else if ($this->IsImage && !$this->ValidateImage()) {
                        die($this->GetMessage());
                }

                else {

                        $FileName = $this->FileName;
                        $TempFileName = $this->TempFileName;
                        $UploadDirectory = $this->UploadDirectory;

                        if (is_uploaded_file($TempFileName)) { 
                                move_uploaded_file($TempFileName, $UploadDirectory . $FileName);
                                return true;
                        } else {
                                return false;
                        }

                }

        }

        #Accessors and Mutators beyond this point.
        #Siginificant documentation is not needed.
        function SetFileName($argv)
        {
                $this->FileName = $argv;
        }

        function SetUploadDirectory($argv)
        {
                $this->UploadDirectory = $argv;
        }

        function SetTempName($argv)
        {
                $this->TempFileName = $argv;
        }

        function SetValidExtensions($argv)
        {
                $this->ValidExtensions = $argv;
        }

        function SetMessage($argv)
        {
                $this->Message = $argv;
        }

        function SetMaximumFileSize($argv)
        {
                $this->MaximumFileSize = $argv;
        }

        function SetEmail($argv)
        {
                $this->Email = $argv;
        }
   
        function SetIsImage($argv)
        {
                $this->IsImage = $argv;
        }

        function SetMaximumWidth($argv)
        {
                $this->MaximumWidth = $argv;
        }

        function SetMaximumHeight($argv)
        {
                $this->MaximumHeight = $argv;
        }   
        function GetFileName()
        {
                return $this->FileName;
        }

        function GetUploadDirectory()
        {
                return $this->UploadDirectory;
        }

        function GetTempName()
        {
                return $this->TempFileName;
        }

        function GetValidExtensions()
        {
                return $this->ValidExtensions;
        }

        function GetMessage()
        {
                if (!isset($this->Message)) {
                        $this->SetMessage("No Message");
                }

                return $this->Message;
        }

        function GetMaximumFileSize()
        {
                return $this->MaximumFileSize;
        }

        function GetEmail()
        {
                return $this->Email;
        }

        function GetIsImage()
        {
                return $this->IsImage;
        }

        function GetMaximumWidth()
        {
                return $this->MaximumWidth;
        }

        function GetMaximumHeight()
        {
                return $this->MaximumHeight;
        }
}

/*** MOTOD DE USU
if( isset($_FILES['imagen']['tmp_name']) && isset($_POST['id']) && isset($_POST['tipoImagen']) ) {
        $tipoImagen = $_POST['tipoImagen'];
        $id = $_POST['id'];

        $upload = new Upload();
        
        $upload->SetFileName($_FILES['imagen']['name']);
        $upload->SetTempName($_FILES['imagen']['tmp_name']);

        $upload->SetValidExtensions(array('gif', 'jpg', 'jpeg', 'png')); 

        if($tipoImagen == "proyecto"){
            $upload->SetUploadDirectory("../../images/proyectos/"); //Upload directory, this should be writable
        }
        if($tipoImagen == "admin"){
            $upload->SetUploadDirectory("../../images/users/"); //Upload directory, this should be writable
        }
        
        
        //$upload->SetEmail("Sidewinder@codecall.net"); //If this is set, an email will be sent each time a file is uploaded.
        //$upload->SetIsImage(true); //If this is set to be true, you can make use of the MaximumWidth and MaximumHeight functions.
        //$upload->SetMaximumWidth(); // Maximum width of images
        //$upload->SetMaximumHeight(400); //Maximum height of images
        
        $upload->SetMaximumFileSize(90000000); //Maximum file size in bytes, if this is not set, the value in your php.ini file will be the maximum value
        
        if($upload->UploadFile()){
            //echo "Imagen subida exitosamente en ".$upload->GetUploadDirectory().$upload->GetFileName();
            $link = $upload->GetUploadDirectory().$upload->GetFileName();
            
            //actualiza la imagen en la base de datos
            if($tipoImagen = "proyecto"){
                //echo '<hr>link :'.$link;
                $proyecto = new Proyectos();
                if($proyecto->setProyectoImagen($link, $id)){
                    echo 'Imagen actualizada correctamente';
                }
            }
        }
}

***/
?> 
