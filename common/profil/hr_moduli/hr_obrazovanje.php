<?
	// pokupi sifrarnike iz baze i zamotaj ih u <option> </option>
	$strucnasprema=getSifrarnikData("sifrarnik_strucna_sprema");
	$zvanja=getSifrarnikData("sifrarnik_akademska_zvanja");
	$naucnaoblast=getSifrarnikData("sifrarnik_naucna_oblast");
	$uzaoblast = getSifrarnikData("sifrarnik_uza_naucna_oblast");
	$fascati = getSifrarnikData("sifrarnik_fascati");
	$fascatipod = getSifrarnikData("sifrarnik_fascati_podoblast"); // napravio sam sifrarnik ali je na hr.unsa.ba prazan
?>

    <div id="Korak4">
        <h2>Obrazovanje</h2>
   <table border="0" width="600">
      <tr>
        <td colspan="2" bgcolor="#999999">
          <font color="#FFFFFF"><b>Podaci o obrazovanju:</b></font>
        </td>
      </tr>
      <tr>
        <td style="height:30px">NAPOMENA:</td>
        <td>
          <b>Molim vas unesite podatke oznacene sa <font color=red>*</font>; Hronoloski</b>
        </td>
      </tr>
      
      <tr>
        <td>Strucna sprema:</td>
        <td>
          <select ><?=$strucnasprema ?></select>  <b><font color=red>*</font></b>
        </td>
      </tr>
      
      <tr>
        <td>Zvanje:</td>
        <td>
          <select ><?=$zvanja ?></select>  <b><font color=red>*</font></b>
        </td>
      </tr>
      
      <tr>
        <td>Naucna oblast:</td>
        <td>
          <select ><?=$naucnaoblast ?></select>  <b><font color=red>*</font></b>
        </td>
      </tr>
      
      
      <tr>
        <td>Uza naucna oblast:</td>
        <td>
          <select ><?=$uzaoblast ?></select>  <b><font color=red>*</font></b>
        </td>
      </tr>
      
      <tr>
        <td>Frascati oblast:</td>
        <td>
          <select ><?=$fascati ?></select>  <b><font color=red>*</font></b>
        </td>
      </tr>
      
      <!--
      <tr>
        <td>Frascati pod-oblast:</td>
        <td>
          <select ><?=$fascatipod ?></select>  <b><font color=red>*</font></b>
        </td>
      </tr> -->
      
      
      <tr>
        <td>Aktivan u nastavi:</td>
        <td>
			<input type="checkbox" name="aktivan">         
		</td>
      </tr>
      
      <tr>
        <td>Zavrsena skola:	</td>
        <td>
          <input type="text"  value="" /> 
        </td>
      </tr>
      
      
      <tr>
        <td>Zavrsen fakultet:</td>
        <td>
          <input type="text"  value="" /> 
        </td>
      </tr>
      
      
      <tr>
        <td>Oblast (opisno):</td>
        <td>
          <input type="text"  value="" /> 
        </td>
      </tr>
      
      <tr>
        <td>Drustvene sposobnosti i vjestine:	</td>
        <td>
          <textarea rows="3" cols="40"></textarea>
        </td>
      </tr>
       <tr>
        <td>Organizacione sposobnosti i vjestine:	</td>
        <td>
          <textarea rows="3" cols="40"></textarea>
        </td>
      </tr>
       <tr>
        <td>Informaticke sposobnostii vjestine:	</td>
        <td>
          <textarea rows="3" cols="40"></textarea>
        </td>
      </tr>
       <tr>
        <td>Ostale informacije:	</td>
        <td>
          <textarea rows="3" cols="40"></textarea>
        </td>
      </tr>
       <tr>
        <td>Dodatne informacije:</td>
        <td>
          <textarea rows="3" cols="40"></textarea>
        </td>
      </tr>
      </table>
      
      

    </div>