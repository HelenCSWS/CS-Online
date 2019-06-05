<?php
import('php2go.util.Number');
import('php2go.net.MailMessage'); 
import('php2go.security.OpenSSLCrypt');


class F60Common extends php2go
{
  
    
    function currency2decimal($currencyVal)
    {
        $decVal = 0;
        if (strlen($currencyVal)!=0)
            $decVal = str_replace(",","",$currencyVal);
            $decVal = str_replace("$","",$currencyVal) + 0.0;
            
        return $decVal;
    }
    
    function sortSymbols() {
        $sortSymbols =array('a' => '&#9650;','d' => '&#9660;');
        return $sortSymbols;
    }
    
    function formatCurrency($value)
    {
        return Number::fromDecimalToCurrency($value,"$", ".", ",", 2, "left");
    }
    
    function formatLast2ZeroAsCurrency($value)
    {
       
        if( substr($value, -2)=="00")
        {
            $value = substr($value, 0, -2) . '';
        }

        return $value;
    }
    
    function percentage($val1, $val2, $precision) 
	{
	 	if($val2!=0)
	 	{
			$division = $val1 / $val2;
		
			$res = $division * 100;
		
			$res = round($res, $precision);
			
			$res =$res."%";
			
			return $res;
		}
		else
			return 0;
	}

	function _sendEmail($to, $bcc, $from, $subject, $body, $from_name="",$attachments=null)
    {    		
		$mail = new MailMessage();
        $mail->setSubject($subject); 
        $mail->setFrom($from,$from_name); 
        
 		// to	
     	$arrayTo = split(";",$to);     
     	for($i=0; $i<count($arrayTo);$i++)
     	{
    	 	$emailTo = $arrayTo[$i];
    	 	if($emailTo!="")
			    $mail->addTo($emailTo);
    	}
    	
	   // $bcc
	    $arrayBcc = split(";",$bcc);
     	  
     	for($i=0; $i<count($arrayBcc);$i++)
     	{
    	 	$emailBcc = $arrayBcc[$i];
    	 	if($emailBcc!="")
			    $mail->addBcc($emailBcc);
    	}    	

		if($attachments!=null)	
		{
	    	if (TypeUtils::isArray($attachments))
			{
			    foreach ($attachments as $attachment)
			    {
			        if ($attachment<>"")
			            $mail->addAttachment($attachment);
			    }
			}
			else if (isset($attachments) && $attachments<>"")
			{
			    $mail->addAttachment($attachments);
			}
		}
	            	   
        $mail->setHtmlBody($body);
        $mail->build(); 
        
        $transport =& $mail->getTransport(); 
        $transport->setType(MAIL_TRANSPORT_MAIL);

		$transport->send();		
        
        
    }
    
    function replaceToken( $token, $replaceVal,$regStr)
	{
	 	$sToken ="[$token]";
		return str_replace($sToken, $replaceVal,$regStr);
	}

    function encrypt_decrypt_code($data,$action)
    {
         # --- ENCRYPTION ---

        # the key should be random binary, use scrypt, bcrypt or PBKDF2 to
        # convert a string into a key
        # key is specified using hexadecimal
        $key = pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
        
        # show key size use either 16, 24 or 32 byte keys for AES-128, 192
        # and 256 respectively
        $key_size =  strlen($key);
        echo "Key size: " . $key_size . "\n";
        
        $plaintext = "This string was AES-256 / CBC / ZeroBytePadding encrypted.";
    
        # create a random IV to use with CBC encoding
      //  $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        
        $iv_size = $key_size;
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        
        # creates a cipher text compatible with AES (Rijndael block size = 128)
        # to keep the text confidential 
        # only suitable for encoded input that never ends with value 00h
        # (because of default zero padding)
        $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key,
                                     $plaintext, MCRYPT_MODE_CBC, $iv);
    
        # prepend the IV for it to be available for decryption
        $ciphertext = $iv . $ciphertext;
        
        # encode the resulting cipher text so it can be represented by a string
        $ciphertext_base64 = base64_encode($ciphertext);
    
        echo  $ciphertext_base64 . "\n";
    
        # === WARNING ===
    
        # Resulting cipher text has no integrity or authenticity added
        # and is not protected against padding oracle attacks.
        
        # --- DECRYPTION ---
        
        $ciphertext_dec = base64_decode($ciphertext_base64);
        
        # retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
        $iv_dec = substr($ciphertext_dec, 0, $iv_size);
        
        # retrieves the cipher text (everything except the $iv_size in the front)
        $ciphertext_dec = substr($ciphertext_dec, $iv_size);
    
        # may remove 00h valued characters from end of plain text
        $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key,
                                        $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
    
        echo  $plaintext_dec . "\n";
    }
    
    function enCodeString($sData)
    {
         $enStr1 = base64_encode($sData);
         $enStr2 = base64_encode($enStr1);

        return $enStr2;
    }
    
    function deCodeString($sData)
    {
        $enStr1     = base64_decode($sData);
        $deString   = base64_decode($enStr1);
        
        return $deString;

    }
    function testEncode()
    {
        echo("<h3> Symmetric Encryption </h3>"); 
        $key_value      = "KEYVALUE"; 
        $plain_text     = "PLAINTEXT"; 
        $encrypted_text = mcrypt_ecb(MCRYPT_DES, $key_value, $plain_text, MCRYPT_ENCRYPT); 
        echo ("<p><b> Text after encryption : </b>"); 
        echo ( $encrypted_text ); 
        $decrypted_text = mcrypt_ecb(MCRYPT_DES, $key_value, $encrypted_text, MCRYPT_DECRYPT); 
        echo ("<p><b> Text after decryption : </b>"); 
        echo ( $decrypted_text ); 
    }
}
?>