<?php
// COMMON/ZAVRSNISTRANE - stranice zavrsnih radova

function common_zavrsniStrane()
{
	//debug mod aktivan
	global $userid, $user_nastavnik, $user_student, $conf_files_path, $user_siteadmin;
	$predmet 	= intval($_REQUEST['predmet']);
	$ag 		= intval($_REQUEST['ag']);
	$zavrsni 	= intval($_REQUEST['zavrsni']);
	$action 	= $_REQUEST['action'];
	
	//stranica za zavrsne radove
	$section 	= $_REQUEST['section'];
	$subaction  = $_REQUEST['subaction'];
	$id			= intval($_REQUEST['id']);  //editing links, rss....

	if ($user_student && !$user_siteadmin) //ordinary student
	{
		$actualZavrsni = getActualZavrsniForUserInPredmet($userid, $predmet, $ag);
		if ($actualZavrsni[id] != $zavrsni)
		{
			//korisnik nije prijavljen an temu na ovom predmetu
			zamgerlog("strane zavrsnih radova: korisnik nije na zavrsnom radu $zavrsni (pp$predmet, ag$ag)", 3);
			return;	
		}
	}
	
	$paramsZavrsni = getPredmetParamsForZavrsni($predmet, $ag);
	$zavrsni1 = getZavrsni($zavrsni);	
	$membersZavrsni = fetchZavrsniMembers($zavrsni1[id]);
	
	if ($paramsZavrsni[zakljucani_zavrsni] == 0)
	{
		zamgerlog("strane zavrsnih radova: jos nisu otvorene! (pp$predmet, ag$ag)", 3);
		return;
	}
	
	if ($user_student && !$user_siteadmin)
		$linkPrefix = "?sta=student/zavrsni&akcija=zavrsnistranica&zavrsni=$zavrsni&predmet=$predmet&ag=$ag";
	elseif ($user_nastavnik)
		$linkPrefix = "?sta=nastavnik/zavrsni&akcija=zavrsni_stranica&zavrsni=$zavrsni&predmet=$predmet&ag=$ag";
	else
		return;

	?>  
     <h2><?=filtered_output_string($zavrsni1[naziv]) ?></h2>
     <div class="links">
            <ul class="clearfix">
            	<li><a href="<?php echo $linkPrefix?>">Početna strana</a></li>
            	<li><a href="<?php echo $linkPrefix . "&section=info"?>">Informacije o temi završnog rada</a></li>
                <li><a href="<?php echo $linkPrefix . "&section=links"?>">Korisni linkovi</a></li>
                <li><a href="<?php echo $linkPrefix . "&section=rss"?>">RSS feedovi</a></li>
                <li><a href="<?php echo $linkPrefix . "&section=bl"?>">Članci</a></li>
                <li><a href="<?php echo $linkPrefix . "&section=file"?>">Fajlovi</a></li>
                <li class="last"><a href="<?php echo $linkPrefix . "&section=bb"?>">Grupa za diskusiju</a></li>
            </ul>   
     </div>	
    <?php	
	
	if (!isset($section))
	{
		//display  start page zavrsni
	?>
  	    <div id="mainWrapper" class="clearfix">
			<div id="leftBlocks">
                <div class="blockRow clearfix">
                     <div class="block" id="latestPosts">
                        <a class="blockTitle" href="<?=$linkPrefix . "&section=bb" ?>" title="Grupa za diskusiju">Najnoviji postovi</a>
                        <div class="items">
                        <?php
                            $latestPostsZavrsni = fetchLatestPostsForZavrsni($zavrsni1[id], 4);
                            foreach ($latestPostsZavrsni as $post)
                            {
                            
                        ?>
                            <div class="item">
                                <span class="date"><?=date('d.m H:i  ', mysql2time($post[vrijeme])) ?></span>
                                <a href="<?=$linkPrefix . "&section=bb&subaction=view&tid=$post[tema]#p$post[id]" ?>" title="<?=$post['naslov']?>" target="_blank"><?php
                                
                                    $maxLen = 100;	
                                    $len = strlen($post[naslov]);
                                    
                                    echo filtered_output_string(substr($post['naslov'], 0, $maxLen-1));
                                    if ($len>$maxLen) 
                                        echo '...';
                                 ?></a>
                                <span class="author"> - <?=filtered_output_string($post[osoba][prezime] . ' ' . $post[osoba][ime]) ?></span>
                                <div class="desc"><?php
                                    $maxLen = 200;	
                                    $len = strlen($post[tekst]);
                                    
                                    echo filtered_output_string(substr($post['tekst'], 0, $maxLen-1));
                                    if ($len>$maxLen) 
                                        echo '...';
    
                             ?></div><!--desc-->
                            </div><!--item-->	
                        <?php
                            }
                        ?>
                        
                        </div><!--items-->
                    </div><!--block-->
                   
                </div><!--blockRow-->
                
                <div class="blockRow clearfix">
                     <div class="block" id="latestArticles">
                        <a class="blockTitle" href="<?=$linkPrefix . "&section=bl" ?>" title="Članci">Najnoviji članci</a>
                        <div class="items">
                        <?php
                            $latestArticlesZavrsni = fetchArticlesForZavrsni($zavrsni1[id], 0, 4);
                            foreach ($latestArticlesZavrsni as $article)
                            {
                                $author = getAuthorOfArticle($article[id]);	
                        ?>
                            <div class="item">
                                <span class="date"><?=date('d.m H:i  ', mysql2time($article[vrijeme])) ?></span>
                                <a href="<?=$linkPrefix . "&section=bl&subaction=view&id=$article[id]" ?>" title="<?=$article['naslov']?>" target="_blank"><?php
                                
                                    $maxLen = 100;	
                                    $len = strlen($article[naslov]);
                                    
                                    echo filtered_output_string(substr($article['naslov'], 0, $maxLen-1));
                                    if ($len>$maxLen) 
                                        echo '...';
                                 ?></a>
                                <span class="author"> - <?=filtered_output_string($author[prezime] . ' ' . $author[ime]) ?></span>
                                <div class="desc"><?php
                                    $maxLen = 200;	
                                    $len = strlen($article[tekst]);
                                    
                                    echo filtered_output_string(substr($article['tekst'], 0, $maxLen-1));
                                    if ($len>$maxLen) 
                                        echo '...';
    
                             ?></div><!--desc-->
                            </div><!--item-->	
                        <?php
                            }
                        ?>
                        
                        </div><!--items-->
                    </div><!--block-->
                    
                </div><!--blockRow-->

            </div><!--leftBlocks-->
            <div id="rightBlocks" class="clearfix">
            	<div class="blockRow">
                    <div class="block" id="latestLinks">
                        <a class="blockTitle" href="<?=$linkPrefix . "&section=links" ?>" title="Korisni linkovi">Korisni linkovi</a>
                        <div class="items">
                       
        <?php
            //get latest entries
            
            $linksZavrsni = fetchLinksForZavrsni($zavrsni1[id], 0, 4);;
            
            foreach ($linksZavrsni as $link)
            {
                            $url = $link[url];
                            $scheme = parse_url($url);
                            $scheme  = $scheme['scheme'];
                        
                            if ($scheme == '') //only www part	
                                $url = 'http://' . $url;
                                
                            $maxLen = 150;	
                            $len = strlen($link[naziv]);
                            
                            
                            if ($len>$maxLen) 
                                echo '...';
    
                            $author = getAuthorOfLinkForZavrsni($link[id]);					
        ?>
                            <div class="item">
                                <a href="<?=$url ?>" title="<?=$link['naziv']?>" target="_blank"><?php
                                
                                    $maxLen = 35;	
                                    $len = strlen($link[naziv]);
                                    
                                    echo filtered_output_string(substr($link['naziv'], 0, $maxLen-1));
                                    if ($len>$maxLen) 
                                        echo '...';
                                 ?></a>
                                <span class="author"> - <?=filtered_output_string($author[prezime] . ' ' . $author[ime]) ?></span>
        <?php
                            if ($link[opis] != '')
                            {
        ?>                   
                                <div class="desc"><?php
                                    $maxLen = 200;	
                                    $len = strlen($link[opis]);
                                    
                                    echo filtered_output_string(substr($link['opis'], 0, $maxLen-1));
                                    if ($len>$maxLen) 
                                        echo '...';
    
                             ?></div><!--desc-->
        <?php                    
                            }
                            
        ?>                 
                            </div><!--item-->   		
        <?php
            
            
            } //foreach
            
            
            
        ?>     
                        </div><!--items-->   
                    </div><!--block--> 
				</div><!--blockRow-->            
            	<div class="blockRow">
                    <div class="block" id="latestRSS">
                        <a class="blockTitle" href="<?=$linkPrefix . "&section=rss" ?>" title="RSS feedovi">RSS feedovi</a>
                        <div class="items">
                       
        <?php
            //get latest entries
            
            $linksZavrsni = fetchRSSForZavrsni($zavrsni1[id], 0, 4);;
            
            foreach ($linksZavrsni as $link)
            {
                            $url = $link[url];
                            $scheme = parse_url($url);
                            $scheme  = $scheme['scheme'];
                        
                            if ($scheme == '') //only www part	
                                $url = 'http://' . $url;
                                
                            $maxLen = 150;	
                            $len = strlen($link[naziv]);
                            
                            
                            if ($len>$maxLen) 
                                echo '...';
    
                        
                        
                            $authorZavrsni = getAuthorOfRSSForZavrsni($link[id]);					
        ?>
                            <div class="item">
                                <a href="<?=$url ?>" title="<?=$link['naziv']?>" target="_blank"><?php
                                
                                    $maxLen = 35;	
                                    $len = strlen($link[naziv]);
                                    
                                    echo filtered_output_string(substr($link['naziv'], 0, $maxLen-1));
                                    if ($len>$maxLen) 
                                        echo '...';
                                 ?></a>
                                <span class="author"> - <?=filtered_output_string($authorZavrsni[prezime] . ' ' . $authorZavrsni[ime]) ?></span>
        <?php
                            if ($link[opis] != '')
                            {
        ?>                   
                                <div class="desc"><?php
                                    $maxLen = 200;	
                                    $len = strlen($link[opis]);
                                    
                                    echo filtered_output_string(substr($link['opis'], 0, $maxLen-1));
                                    if ($len>$maxLen) 
                                        echo '...';
    
                             ?></div><!--desc-->
        <?php                    
                            }
                            
        ?>                 
                            </div><!--item-->   		
        <?php
            
            
            } //foreach
            
            
            
        ?>     
                        </div><!--items-->   
                    </div><!--block-->
                </div><!--blockRow-->  
            	<div class="blockRow">
                    <div class="block" id="latestFiles">
                        <a class="blockTitle" href="<?=$linkPrefix . "&section=file" ?>" title="Fajlovi">Fajlovi</a>
                        <div class="items">
                       
        <?php
            //get latest entries
            
            $filesZavrsni = fetchFilesForZavrsniLatestRevisions($zavrsni1[id], 0, 4);;
            
            foreach ($filesZavrsni as $file)
            {
			
                            $authorZavrsni = getAuthorOfFileForZavrsni($file[id]);					
        ?>
                            <div class="item">
                                <span class="date"><?=date('d.m H:i  ', mysql2time($file[vrijeme])) ?></span>
                                <a href="<?="index.php?sta=common/attachment&tip=zavrsni&zavrsni=$zavrsni&id=$file[id]" ?>" title="<?=$file['filename']?>" ><?php
                                
                                    $maxLen = 100;	
                                    $len = strlen($file[filename]);
                                    
                                    echo filtered_output_string(substr($file['filename'], 0, $maxLen-1));
                                    if ($len>$maxLen) 
                                        echo '...';
                                 ?></a>
                                <span class="authorZavrsni"> - <?=filtered_output_string($authorZavrsni[prezime] . ' ' . $authorZavrsni[ime]) ?></span>
                               
                            </div><!--item-->	
        <?php
            
            
            } //foreach
            
            
            
        ?>     
                        </div><!--items-->   
                    </div><!--block-->
                </div><!--blockRow-->            
                          
            </div><!--rightBlocks-->
        </div><!--mainWrapper-->    
    <?php
	
	} //section not set
	else
	{
		if ($section == 'info')
		{
			// display zavrsni info
	?>
    	<h2>Informacije o temi završnog rada</h2>

<table class="zavrsni" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <th width="200" align="left" valign="top" scope="row">Naziv</th>
    <td width="490" align="left" valign="top"><?=filtered_output_string($zavrsni1['naziv'])?></td>
  </tr>
  <tr>
    <th width="200" align="left" valign="top" scope="row">Prijavljeni studenti</th>
    <td width="490" align="left" valign="top">
    	<?php
			if (empty($membersZavrsni))
				echo 'Nema prijavljenih studenata.';
			else
			{
		?>
        <ul>
        <?php
				foreach ($membersZavrsni as $member)
				{
		?>
        	<li><?=filtered_output_string($member[prezime] . ' ' . $member[ime] . ', ' . $member[brindexa]); ?></li>
		<?php		
				}
		?>
        </ul>	
		<?php	
			}
		
		?>
    
    </td>
  </tr>
  <tr>
    <th width="200" align="left" valign="top" scope="row">Opis</th>
    <td width="490" align="left" valign="top"><?=filtered_output_string($zavrsni1['opis'])?></td>
  </tr>
</table>
    
     
    <?php
		
		} //section -- info
		elseif ($section == 'links')
		{
			//links management
			$linkPrefix .='&section=links';
	?>
<h2>Korisni linkovi</h2>
 <div class="links" id="link">
    <ul class="clearfix">
        <li><a href="<?php echo $linkPrefix?>">Lista linkova</a></li>
        <li><a href="<?php echo $linkPrefix . "&subaction=add"?>">Novi link</a></li>
    </ul>   
</div>	

    <?php	
			if (!isset($subaction))
			{
				$rowsPerPage = 20;
				$pageNum = 1;
				if(isset($_REQUEST['page']))
				{
					$pageNum = $_REQUEST['page'];
				}
				// counting the offset
				$offset = ($pageNum - 1) * $rowsPerPage;
				
				//display links for this finall, with links to edit and delete
				$linksZavrsni = fetchLinksForZavrsni($zavrsni1[id], $offset, $rowsPerPage);
				foreach ($linksZavrsni as $link)
				{
					if (isUserAuthorOfLinkForZavrsni($link[id], $userid))
					{
	?>
<div class="links" id="link">
    <ul class="clearfix">
        <li><a href="<?php echo $linkPrefix . "&subaction=edit&id=$link[id]"?>">Uredi</a></li>
        <li><a href="<?php echo $linkPrefix . "&subaction=del&id=$link[id]"?>">Briši</a></li>
    </ul>   
</div>	
	<?php
					} //if user is author of this item
	?>

<table class="linkovi" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <th width="200" align="left" valign="top" scope="row">URL</th>
    <td width="490" align="left" valign="top">
    <?php
						$url = $link[url];
						$scheme = parse_url($url);
						$scheme  = $scheme['scheme'];
					
						if ($scheme == '') //only www part	
							$url = 'http://' . $url;
						
						
	?><a href="<?=$url ?>" title="<?=$link['naziv']?>" target="_blank"><?=filtered_output_string($link[naziv]); ?></a>   
    </td>
  </tr>
 <?php
 						if ($link['opis'] != '')
						{
 ?>
  <tr>
    <th width="200" align="left" valign="top" scope="row">Opis</th>
    <td width="490" align="left" valign="top"><?=filtered_output_string($link['opis'])?></td>
  </tr>
  <?php
  						} //opis
  ?>
</table>
    <?php
				} //foreach link
				$numrowsZavrsni = getCountLinksForZavrsni($zavrsni1[id]);
							
				$maxPage = ceil($numrows/$rowsPerPage);
				$self = $linkPrefix;
				
				if ($maxPage > 0)
				{
					echo "<span class=\"newsPages\">";
					if ($pageNum > 1)
					{
						$page = $pageNum - 1;
						$prev = " <a href=\"$self&page=$page\">[Prethodna]</a> ";
						
						$first = " <a href=\"$self&page=1\">[Prva]</a> ";
					} 
					
					if ($pageNum < $maxPage)
					{
						$page = $pageNum + 1;
						$next = " <a href=\"$self&page=$page\">[Sljedeća]</a> ";
						
						$last = " <a href=\"$self&page=$maxPage\">[Zadnja]</a> ";
					} 
					
					echo $first . $prev . " Strana <strong>$pageNum</strong> od ukupno <strong>$maxPage</strong> " . $next . $last;
					echo "</span>"; //newsPages span	
				}
				
			} //subactin not set
			else
			{
				if ($subaction == 'add')
				{
					
					if (!isset($_REQUEST['submit']))
					{
				
	?>
						 <h3>Novi link</h3>
				<?php
					print genform("POST", "addForm");
				?>
						
						<div id="formDiv">
							Polja sa * su obavezna. <br />
							
                            <div class="row">
								<span class="label">Naziv *</span>
								<span class="formw"><input name="naziv" type="text" id="naziv" size="70" /></span> 
							</div>
	
							<div class="row">
								<span class="label">URL *</span>
								<span class="formw"><input name="url" type="text" id="url" size="70" /></span> 
							</div>
							<div class="row">
								<span class="label">Opis</span>
								<span class="formw"><textarea name="opis" cols="60" rows="15" wrap="physical" id="opis"></textarea></span>
							</div> 
							
							<div class="row">	
								<span class="formw" style="margin-left:150px;"><input name="submit" type="submit" id="submit" value="Potvrdi"/></span>
							</div>
						

						</div><!--formDiv-->
						</form>
							
	<?php	
					} //not submitted yet
					else
					{
						$errorText = formProcess_links('add');
						if($errorText == '')
						{
							nicemessage('Novi link uspješno dodan.');
							zamgerlog("dodao link na temu završnog rada $zavrsni (pp$predmet)", 2);
							$link = $linkPrefix;
						}
						else
						{	
							niceerror($errorText);
							$link = "javascript:history.back();";		
						}
						nicemessage('<a href="'. $link .'">Povratak.</a>');
					
					
					} //submitted the form
				
				} //subaction == add
				elseif ($subaction == 'edit')
				{
					if (!isUserAuthorOfLinkForZavrsni($id, $userid))
						return;
					
					
					//edit item
					if (!isset($_REQUEST['submit']))
					{
						$entry = getLinkZavrsni($id);
				
		?>
					 <h3>Uredi link</h3>
				<?php
					print genform("POST", "editForm");
				?>
                	
					<div id="formDiv">
						Polja sa * su obavezna. <br />
						
						<div class="row">
							<span class="label">Naziv *</span>
							<span class="formw"><input name="naziv" type="text" id="naziv" size="70" value="<?php echo $entry['naziv']?>" /></span> 
						</div>

						<div class="row">
							<span class="label">URL *</span>
							<span class="formw"><input name="url" type="text" id="url" size="70" value="<?php echo $entry['url']?>" /></span> 
						</div>
						<div class="row">
							<span class="label">Opis</span>
							<span class="formw"><textarea name="opis" cols="60" rows="15" wrap="physical" id="opis"><?php echo $entry['opis']?></textarea></span>
						</div> 
						
						<div class="row">	
							<span class="formw" style="margin-left:150px;"><input name="submit" type="submit" id="submit" value="Potvrdi"/></span>
						</div>
					
					</div><!--formDiv-->
					</form>
							
						
		<?php				
								
					}
					else
					{
						$errorText = formProcess_links('edit');
						if($errorText == '')
						{
							nicemessage('Uspješno ste uredili link.');
							zamgerlog("uredio link na temi završnog rada $zavrsni (pp$predmet)", 2);
							$link = $linkPrefix;
						}
						else
						{	
							//an error occured trying to process the form
							niceerror($errorText);
							$link = "javascript:history.back();";	
							
						}
						nicemessage('<a href="'. $link .'">Povratak.</a>');
						
					} //submitted the form
							
				
				} //subaction == edit
				elseif ($subaction == 'del')
				{
					if (!isUserAuthorOfLinkForZavrsni($id, $userid))
						return;

					//delete item
					if (isset($id) && is_int($id) && $id > 0)
					{
						if (!isset($_REQUEST['c']))
						{
							echo "Da li ste sigurni da zelite obrisati ovaj link?<br />";	
							echo '<a href="' . $linkPrefix . '&subaction=del&id=' . $id . '&c=true">Da</a> | <a href="' . $linkPrefix . '">Odustani</a>';
						}
						else
						{
							if ($_REQUEST['c'] == 'true')
							{
								//delete the record
								if (deleteLinkZavrsni($id))
								{
									nicemessage('Uspješno ste obrisali link.');	
									zamgerlog("obrisao link na temi završnog rada $zavrsni (pp$predmet)", 2);
									$link = $linkPrefix;
								}
								else
								{
									niceerror('Doslo je do greske prilikom brisanja linka. Molimo kontaktirajte administratora.');
									$link = "javascript:history.back();";	
								}
								nicemessage('<a href="'. $link .'">Povratak.</a>');
							}
							
						} //else isset c get parameter
								
						
					} //id is okay		
				
				} //subaction == del
	
			} //subaction set
			
			
		} //section == links
		elseif ($section == 'rss')
		{
			//links management
			$linkPrefix .='&section=rss';
	?>
<h2>RSS feedovi</h2>
 <div class="links clearfix" id="rss">
    <ul>
        <li><a href="<?php echo $linkPrefix?>">Lista RSS feedova</a></li>
        <li><a href="<?php echo $linkPrefix . "&subaction=add"?>">Novi RSS feed</a></li>
    </ul>   
</div>	

    <?php	
			if (!isset($subaction))
			{
				$rowsPerPage = 20;
				$pageNum = 1;
				if(isset($_REQUEST['page']))
				{
					$pageNum = $_REQUEST['page'];
				}
				// counting the offset
				$offset = ($pageNum - 1) * $rowsPerPage;
				
				//display links for this finall, with links to edit and delete
				$feedsZavrsni = fetchRSSForZavrsni($zavrsni1[id], $offset, $rowsPerPage);
				foreach ($feedsZavrsni as $link)
				{
					if (isUserAuthorOfRSSForZavrsni($link[id], $userid))
					{
	?>
<div class="links clearfix" id="rss">
    <ul>
        <li><a href="<?php echo $linkPrefix . "&subaction=edit&id=$link[id]"?>">Uredi</a></li>
        <li><a href="<?php echo $linkPrefix . "&subaction=del&id=$link[id]"?>">Briši</a></li>
    </ul>   
</div>	
	<?php
					} //if user is author of this item
	?>
<table class="rss" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <th width="200" align="left" valign="top" scope="row">URL</th>
    <td width="490" align="left" valign="top">
    <?php
						$url = $link[url];
						$scheme = parse_url($url);
						$scheme  = $scheme['scheme'];
					
						if ($scheme == '') //only www part	
							$url = 'http://' . $url;
						
						
	?><a href="<?=$url ?>" title="<?=$link['naziv']?>" target="_blank"><?=filtered_output_string($link[naziv]); ?></a>   
    </td>
  </tr>
 <?php
 						if ($link['opis'] != '')
						{
 ?>
  <tr>
    <th width="200" align="left" valign="top" scope="row">Opis</th>
    <td width="490" align="left" valign="top"><?=filtered_output_string($link['opis'])?></td>
  </tr>
 <?php
 						} //opis
 ?>

 <tr>
 	<td colspan="2">
 	<?php 
			global $conf_files_path; //Ukljucimo koristenje globalne varijable koja pokazuje na privatni direktorij sa datotekama koji nije dostupan preko weba
 			$hashfromURL = hash("md5",$url);
 			
			$cachefile = "";
 			//Provjeri postojanost $conf_files_path/cache/rss direktorija
			if(file_exists($conf_files_path."/cache")){
				if(!file_exists($conf_files_path."/cache/rss")){
					mkdir($conf_files_path."/cache/rss");
				}
				$cachefile = $conf_files_path."/cache/rss/".$hashfromURL.".html";					
			}else{
				if(mkdir($conf_files_path."/cache")){
					if(mkdir($conf_files_path."/cache/rss/")){
						$cachefile = $conf_files_path."/cache/rss/".$hashfromURL.".html";
					}
				}	
			}	
 						
 			$cachetime = 5*60; //5 minuta TODO:Pri deployment-u povecati na sat-dva.
 			//Serviraj is kesha ako je mladji od $cachetime 
			if(file_exists($cachefile) && (time() - filemtime($cachefile) < $cachetime ))
			{
				include($cachefile);
				print "RSS ucitan iz kesha!";
				
			}
			else{//Ucitaj RSS ponovo	
 						
				$XMLfilename = $url;
			
				//Pocni dump buffera
				ob_start();
			
				include("lib/rss2html.php");//HTML parsiran sadrzaj RSS-a
			
				//Otvori kesh fajl za pisanje
				$fp = fopen($cachefile, 'w');
			
				//Sacuvaj sadrzaj izlaznog buffer-a u fajl
				fwrite($fp, ob_get_contents());
			
				//zatvori fajl
				fclose($fp);
			
				//Posalji izlaz na browser
				ob_end_flush();	
				print "RSS osvjezen - feed ponovo ucitan!";
			}
			
 	?>
 	</td>
</tr>
 
</table>
    <?php
				} //foreach link
				$numrowsZavrsni = getCountRSSForZavrsni($zavrsni1[id]);
							
				$maxPage = ceil($numrows/$rowsPerPage);
				$self = $linkPrefix;
				
				if ($maxPage > 0)
				{
					echo "<span class=\"newsPages\">";
					if ($pageNum > 1)
					{
						$page = $pageNum - 1;
						$prev = " <a href=\"$self&page=$page\">[Prethodna]</a> ";
						
						$first = " <a href=\"$self&page=1\">[Prva]</a> ";
					} 
					
					if ($pageNum < $maxPage)
					{
						$page = $pageNum + 1;
						$next = " <a href=\"$self&page=$page\">[Sljedeća]</a> ";
						
						$last = " <a href=\"$self&page=$maxPage\">[Zadnja]</a> ";
					} 
					
					echo $first . $prev . " Strana <strong>$pageNum</strong> od ukupno <strong>$maxPage</strong> " . $next . $last;
					echo "</span>"; //newsPages span	
				}
				
			} //subactin not set
			else
			{
				if ($subaction == 'add')
				{
					
					if (!isset($_REQUEST['submit']))
					{
				
	?>
						 <h3>Novi RSS feed</h3>
				<?php
					print genform("POST", "addForm");
				?>
						
						<div id="formDiv">
							Polja sa * su obavezna. <br />
							
                            <div class="row">
								<span class="label">Naziv *</span>
								<span class="formw"><input name="naziv" type="text" id="naziv" size="70" /></span> 
							</div>
	
							<div class="row">
								<span class="label">URL *</span>
								<span class="formw"><input name="url" type="text" id="url" size="70" /></span> 
							</div>
							<div class="row">
								<span class="label">Opis</span>
								<span class="formw"><textarea name="opis" cols="60" rows="15" wrap="physical" id="opis"></textarea></span>
							</div> 
							
							<div class="row">	
								<span class="formw" style="margin-left:150px;"><input name="submit" type="submit" id="submit" value="Potvrdi"/></span>
							</div>
						
						</div><!--formDiv-->
						</form>
							
	<?php	
					} //not submitted yet
					else
					{
						$errorText = formProcess_rss('add');
						if($errorText == '')
						{
							nicemessage('Novi RSS feed uspješno dodan.');
							zamgerlog("dodao novi rss feed na temu završnog rada $zavrsni (pp$predmet)", 2);
							$link = $linkPrefix;
						}
						else
						{	
							niceerror($errorText);
							$link = "javascript:history.back();";		
						}
						nicemessage('<a href="'. $link .'">Povratak.</a>');
					
					
					} //submitted the form
				
				} //subaction == add
				elseif ($subaction == 'edit')
				{
					if (!isUserAuthorOfRSSForZavrsni($id, $userid))
						return;
					
					//edit item
					
					if (!isset($_REQUEST['submit']))
					{
						$entry = getRSSZavrsni($id);
				
		?>
					 <h3>Uredi RSS feed</h3>
				<?php
					print genform("POST", "editForm");
				?>
                	
					<div id="formDiv">
						Polja sa * su obavezna. <br />
						
						<div class="row">
							<span class="label">Naziv *</span>
							<span class="formw"><input name="naziv" type="text" id="naziv" size="70" value="<?php echo $entry['naziv']?>" /></span> 
						</div>

						<div class="row">
							<span class="label">URL *</span>
							<span class="formw"><input name="url" type="text" id="url" size="70" value="<?php echo $entry['url']?>" /></span> 
						</div>
						<div class="row">
							<span class="label">Opis</span>
							<span class="formw"><textarea name="opis" cols="60" rows="15" wrap="physical" id="opis"><?php echo $entry['opis']?></textarea></span>
						</div> 
						
						<div class="row">	
							<span class="formw" style="margin-left:150px;"><input name="submit" type="submit" id="submit" value="Potvrdi"/></span>
						</div>
					
					</div><!--formDiv-->
					</form>
							
						
		<?php				
								
					}
					else
					{
						$errorText = formProcess_rss('edit');
						if($errorText == '')
						{
							nicemessage('Uspješno ste uredili RSS feed.');
							zamgerlog("uredio rss feed na temi završnog rada $zavrsni (pp$predmet)", 2);
							$link = $linkPrefix;
						}
						else
						{	
							//an error occured trying to process the form
							niceerror($errorText);
							$link = "javascript:history.back();";	
							
						}
						nicemessage('<a href="'. $link .'">Povratak.</a>');
						
					} //submitted the form
							
				
					
				} //subaction == edit
				elseif ($subaction == 'del')
				{
					if (!isUserAuthorOfRSSForZavrsni($id, $userid))
						return;

					//delete item
					if (isset($id) && is_int($id) && $id > 0)
					{
						if (!isset($_REQUEST['c']))
						{
							echo "Da li ste sigurni da zelite obrisati ovaj RSS feed?<br />";	
							echo '<a href="' . $linkPrefix . '&subaction=del&id=' . $id . '&c=true">Da</a> | <a href="' . $linkPrefix . '">Odustani</a>';			
						}
						else
						{
							if ($_REQUEST['c'] == 'true')
							{
								//delete the record
								if (deleteRSSZavrsni($id))
								{
									nicemessage('Uspješno ste obrisali RSS feed.');	
									zamgerlog("obrisao rss feed na temizavršnog rada $zavrsni (pp$predmet)", 2);
									$link = $linkPrefix;
								}
								else
								{
									niceerror('Doslo je do greske prilikom brisanja RSS feeda. Molimo kontaktirajte administratora.');
									$link = "javascript:history.back();";	
								}
								nicemessage('<a href="'. $link .'">Povratak.</a>');
							}
							
						} //else isset c get parameter
								
						
					} //id is okay		
				
				} //subaction == del
	
			} //subaction set
			
			
		} //section == rss
		elseif ($section == 'bl')
		{
			//links management
			$linkPrefix .='&section=bl';
    ?>
<h2>Članci</h2>
 <div class="links clearfix" id="bl">
    <ul>
        <li><a href="<?php echo $linkPrefix?>">Lista članaka</a></li>
        <li><a href="<?php echo $linkPrefix . "&subaction=add"?>">Novi članak</a></li>
    </ul>   
</div>	
    <?php
			if (!isset($subaction))
			{
				
				$rowsPerPage = 20;
				$pageNum = 1;
				if(isset($_REQUEST['page']))
				{
					$pageNum = $_REQUEST['page'];
				}
				// counting the offset
				$offset = ($pageNum - 1) * $rowsPerPage;
				
				$articlesZavrsni = fetchArticlesForZavrsni($zavrsni1[id], $offset, $rowsPerPage);
				foreach($articlesZavrsni as $article)
				{
	?>
    
   <div class="article_summary clearfix">
   	<?php
		if (!empty($article[slika]))
		{
	?>
    	<div class="imgCont">
        	<a href="<?="index.php?sta=common/articleImageDownloadZavrsni&zavrsni=$zavrsni&predmet=$predmet&ag=$ag&a=$article[id]&u=$article[osoba]&i=$article[slika]" ?>" target="_blank">
    			<img src="<?="index.php?sta=common/articleImageDownloadZavrsni&zavrsni=$zavrsni&predmet=$predmet&ag=$ag&a=$article[id]&u=$article[osoba]&i=$article[slika]"?>" />
        	</a>
        </div>
	<?php
		}
	?>
    	<div class="contentCont" <?php if (empty($article[slika])) echo 'style="margin-left: 0;"' ?>>
            <h1>
                <a href="<?=$linkPrefix . "&subaction=view&id=$article[id]" ?>" 
                title="<?=$article['naslov'] ?>"><?=filtered_output_string($article['naslov']) ?>
                </a>
            </h1>
            <div class="details">
        <?php
			$authorZavrsni = getAuthorOfArticle($article[id]);
		?>
                Autor: <?=filtered_output_string($authorZavrsni[ime] . ' ' . $authorZavrsni[prezime]) ?><br />
                Datum: <?=date('d.m.Y', strtotime($article[vrijeme])) ?>
            </div><!--details-->
   <?php
   		if (isUserAuthorOfArticle($article[id], $userid) == true)
		{
	?>	
            <div class="buttons">
                <a href="<?= $linkPrefix . "&subaction=edit&id=$article[id]" ?>" title="Uredi ovaj članak">Uredi</a> | 
                <a href="<?= $linkPrefix . "&subaction=del&id=$article[id]" ?>" title="Briši ovaj članak">Briši</a>
            </div><!--buttons-->	
	<?php	
		}
   ?>

<div class="text">
                                <?php
                                $len = strlen($article[tekst]);
                        
                                if (!empty($article[slika]))
                                    $maxLen = 400;	
                                else
                                    $maxLen = 800;	
                                echo filtered_output_string(substr($article['tekst'], 0, $maxLen-1));
                                if ($len>$maxLen) 
                                    echo '...';
                                ?>
            </div><!--text-->
        </div><!--contentCont-->
   </div><!--article_summary--> 
    
    <?php
				} //foreach article	
				$numrowsZavrsni = getCountArticlesForZavrsni($zavrsni1[id]);
							
				$maxPage = ceil($numrows/$rowsPerPage);
				$self = $linkPrefix;
				
				if ($maxPage > 0)
				{
					echo "<span class=\"newsPages\">";
					if ($pageNum > 1)
					{
						$page = $pageNum - 1;
						$prev = " <a href=\"$self&page=$page\">[Prethodna]</a> ";
						
						$first = " <a href=\"$self&page=1\">[Prva]</a> ";
					} 
					
					if ($pageNum < $maxPage)
					{
						$page = $pageNum + 1;
						$next = " <a href=\"$self&page=$page\">[Sljedeća]</a> ";
						
						$last = " <a href=\"$self&page=$maxPage\">[Zadnja]</a> ";
					} 
					
					echo $first . $prev . " Strana <strong>$pageNum</strong> od ukupno <strong>$maxPage</strong> " . $next . $last;
					echo "</span>"; //newsPages span	
				}
							
			} //subactin not set
			else
			{
				if ($subaction == 'view')
				{
					$articleZavrsni = getArticle($id);
					if (empty($articleZavrsni))
					{
						zamgerlog("strane završnih radova: nepostojeci clanak sa IDom $id, tema $zavrsni (pp$predmet, ag$ag)", 3);
						return;
					}
		?>
		
	   <div class="article_full clearfix">
			<div class="contentCont clearfix">
				<h1>
					<a href="<?=$linkPrefix . "" ?>" 
					title="<?=$article['naslov'] ?>"><?=filtered_output_string($article['naslov']) ?>
					</a>
				</h1>
				<div class="details">
			<?php
				$authorZavrsni = getAuthorOfArticle($article[id]);
			?>
					Autor: <?=filtered_output_string($authorZavrsni[ime] . ' ' . $authorZavrsni[prezime]) ?><br />
					Datum: <?=date('d.m.Y', strtotime($article[vrijeme])) ?>
				</div><!--details-->
	   <?php
					if (isUserAuthorOfArticle($article[id], $userid) == true)
					{
		?>	
				<div class="buttons">
					<a href="<?= $linkPrefix . "&subaction=edit&id=$article[id]" ?>" title="Uredi ovaj članak">Uredi</a> | 
					<a href="<?= $linkPrefix . "&subaction=del&id=$article[id]" ?>" title="Briši ovaj članak">Briši</a>
				</div><!--buttons-->	
		<?php	
					}
	   ?>
		<?php
					if (!empty($article[slika]))
					{
		?>
			<div class="imgCont">
            	<a href="<?="index.php?sta=common/articleImageDownloadZavrsni&zavrsni=$zavrsni&predmet=$predmet&ag=$ag&a=$article[id]&u=$article[osoba]&i=$article[slika]" ?>" target="_blank">
            		<img src="<?="index.php?sta=common/articleImageDownloadZavrsni&zavrsni=$zavrsni&predmet=$predmet&ag=$ag&a=$article[id]&u=$article[osoba]&i=$article[slika]"?>" />
                </a>     
            </div>
	  <?php
					}
		?>
				<div class="text"><?=filtered_output_string($article['tekst']) ?></div><!--text-->
			</div><!--contentCont-->
	   </div><!--article_full--> 
       <a id="backLink" href="<?=$linkPrefix?>">Povratak na listu članaka</a>

		
		<?php
					
				
				} //subaction == view
				elseif ($subaction == 'add')
				{
		
					if (!isset($_REQUEST['submit']))
					{
		
	?>	
    		
				 <h3>Novi članak</h3>
				<?php
					print genform("POST", "addForm\" enctype=\"multipart/form-data\" ");
				?>
                
                <div id="formDiv">
                	Polja sa * su obavezna. <br />
                
                	<div class="row">
                        <span class="label">Naslov *</span>
                        <span class="formw"><input name="naslov" type="text" id="naslov" size="70" /></span> 
                  	</div>
                    <div class="row">
                        <span class="label">Tekst</span>
                        <span class="formw"><textarea name="tekst" cols="60" rows="15" wrap="physical" id="tekst"></textarea></span>
                   	</div> 
                   
                    <div class="row">
                        <span class="label">Slika</span>
                        <span class="formw">
                            <input name="image" type="file" id="image" size="60" />
                        </span><br /><br />
                        Dozvoljeni tipovi slike: jpg, jpeg, gif, png <br />
                    </div> 
                    
                    <div class="row">	
                      	<span class="formw" style="margin-left:150px;"><input name="submit" type="submit" id="submit" value="Potvrdi"/></span>
                    </div>
                
                </div><!--formDiv-->
               
                </form>
                
			
	<?php	
					} //not submitted yet
					else
					{
						$errorText = formProcess_bl('add');
						if($errorText == '')
						{
							nicemessage('Novi članak uspješno dodan.');
							zamgerlog("dodao novi clanak na temu završnog rada $zavrsni (pp$predmet)", 2);
							$link = $linkPrefix;
						}
						else
						{	
							niceerror($errorText);
							$link = "javascript:history.back();";		
						}
						nicemessage('<a href="'. $link .'">Povratak.</a>');
					
					
					} //submitted the form
	
				} //subaction == add
				elseif ($subaction == 'edit')
				{
					if (!isUserAuthorOfArticle($id, $userid))
						return;

					
					//edit item
					if (!isset($_REQUEST['submit']))
					{
						$entryZavrsni = getArticle($id);
			
?>
				 <h3>Uredi članak</h3>
				<?php
					print genform("POST", "editForm\" enctype=\"multipart/form-data\" ");
				?>
				
				<div id="formDiv">
					Polja sa * su obavezna. <br />
				
					<div class="row">
						<span class="label">Naslov *</span>
						<span class="formw"><input name="naslov" type="text" id="naslov" size="70" value="<?php echo $entry['naslov']?>" /></span> 
					</div>
					<div class="row">
						<span class="label">Tekst</span>
						<span class="formw"><textarea name="tekst" cols="60" rows="15" wrap="physical" id="tekst"><?php echo $entry['tekst'] ?></textarea></span>
					</div> 

<?php 					if ($entry['slika'] != '')
						{
						//if the image exists, display it
			  ?>
				   <div class="row">
						<span class="label">Trenutna slika</span>
						<span class="formw"><img src="<?="index.php?sta=common/articleImageDownloadZavrsni&zavrsni=$zavrsni&predmet=$predmet&ag=$ag&a=$article[id]&u=$entry[osoba]&i=$entry[slika]"?>" />
						</span>
				   </div> 
				   
				   <div class="row">
						<span class="label">Briši sliku</span>
						<span class="formw"><input name="delete" type="checkbox" id="delete" value="delete" /></span>
				   </div> 
			   
<?php
						} //if image is present
			  
?>
					<div class="row">
					  <span class="label"><?php 
					  if($entry['slika'] != '') echo "ILI: Zamijeni sliku"; else echo "Slika";?></span>
						<span class="formw">
							<input name="image" type="file" id="image" size="50" />
						</span><br /><br />
						Dozvoljeni tipovi slike: jpg, jpeg, gif, png <br />
					</div>                         
					
					<div class="row">	
						<span class="formw" style="margin-left:150px;"><input name="submit" type="submit" id="submit" value="Potvrdi"/></span>
					</div>
				
				</div><!--formDiv-->
				
				
				</form>
				
				
<?php				
						
					}
					else
					{
						$errorText = formProcess_bl('edit');
						if($errorText == '')
						{
							nicemessage('Uspješno ste uredili članak.');
							zamgerlog("uredio clanak na temu završnog rada $zavrsni (pp$predmet)", 2);
							$link = $linkPrefix;
						}
						else
						{	
							//an error occured trying to process the form
							niceerror($errorText);
							$link = "javascript:history.back();";	
							
						}
						nicemessage('<a href="'. $link .'">Povratak.</a>');
						
					} //submitted the form
				
				} //subaction == edit
				elseif ($subaction == 'del')
				{
					//delete item
					if (isset($id) && is_int($id) && $id > 0)
					{
						if (isUserAuthorOfArticle($id, $userid) == false)
							return;
						
						if (!isset($_REQUEST['c']))
						{
							echo "Da li ste sigurni da zelite obrisati ovaj članak? <br />";	
							echo '<a href="' . $linkPrefix .'&amp;subaction=del&amp;id=' . $id . '&amp;c=true">Da</a> | <a href="' . $linkPrefix . '">Odustani</a>';			
						}
						else
						{
							if ($_REQUEST['c'] == 'true')
							{
								//delete the record
								if (deleteArticleZavrsni($id))
								{
									nicemessage('Uspješno ste obrisali članak.');	
									zamgerlog("obrisao clanak na projektu $projekat (pp$predmet)", 2);
									$link = $linkPrefix;
								}
								else
								{
									niceerror('Došlo je do greske prilikom brisanja članka. Molimo kontaktirajte administratora.');
									$link = "javascript:history.back();";	
								}
								nicemessage('<a href="'. $link .'">Povratak.</a>');
							}
							
						 } //else isset c get parameter
								
						
					  } //id is okay		
				
				 } //subaction == del
		
			} //subaction set
				
		} //section == bl (blackboard)
		elseif ($section == 'file')
		{
			//files management
			$linkPrefix .='&section=file';

	?>
<h2>Fajlovi</h2>
 <div class="links clearfix" id="rss">
    <ul>
        <li><a href="<?php echo $linkPrefix?>">Lista fajlova</a></li>
        <li><a href="<?php echo $linkPrefix . "&subaction=add"?>">Novi fajl</a></li>
    </ul>   
</div>	

    <?php	
			if (!isset($subaction))
			{
				$rowsPerPage = 20;
				$pageNum = 1;
				if(isset($_REQUEST['page']))
				{
					$pageNum = $_REQUEST['page'];
				}
				// counting the offset
				$offset = ($pageNum - 1) * $rowsPerPage;			
				
				//display files for this project, with links to edit and delete
				$filesZavrsni = fetchFilesForZavrsniAllRevisions($zavrsni1[id], $offset, $rowsPerPage);
	?>
<table class="files_table" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th scope="col" class="creation_date">Datum kreiranja</th>
    <th scope="col" class="author">Autor</th>
    <th scope="col" class="revision">Revizija</th>
    <th scope="col" class="name">Naziv</th>
    <th scope="col" class="filesize">Veličina</th>
    <th scope="col" class="options">Opcije</th>
  </tr>
	<?php
				foreach ($filesZavrsni as $file)
				{
					$lastRevisionId = 0;
					$firstRevisionId = count($file) > 0 ? count($file) - 1 : 0;
					$authorZavrsni = getAuthorOfFileForZavrsni($file[$lastRevisionId][id]);
	?>				
    <tr>
    	<td class="creation_date"><?=date('d.m.Y H:i:s', mysql2time($file[$lastRevisionId][vrijeme]))?></td><!--vrijeme-->
    	<td class="author"><?=filtered_output_string($authorZavrsni['ime'] . ' ' . $authorZavrsni['prezime']) ?></td><!--author-->
        <td class="revision">v<?=$file[$lastRevisionId][revizija] ?></td><!--revizija-->
        <td class="filename"><?php 
					if (count($file) > 1)
					{
	?>
		<a href="#" onClick="toggleFileRevisions('file_<?=$file[$lastRevisionId][id] ?>_revisions')"><?=filtered_output_string($file[$lastRevisionId][filename]) ?></a>		
   	<?php
    				}
					else
					{
	?>
    	<?=filtered_output_string($file[$lastRevisionId][filename]) ?>
    <?php						
					}
					
    ?>        </td><!--filename-->
        <td class="filesize"><?php
        	$lokacijafajlova ="$conf_files_path/zavrsni/fajlovi/$zavrsni/" . $file[$lastRevisionId][osoba] . "/" . 
			$file[$lastRevisionId][filename] . '/v' . $file[$lastRevisionId][revizija] . '/';
			$filepath = $lokacijafajlova . $file[$lastRevisionId][filename];
			$filesize = filesize($filepath);
			echo nicesize($filesize);
			?>        </td><!--filesize-->
        <td class="options">
			<a href="<?='index.php?sta=common/attachment' . "&tip=zavrsni&zavrsni=$zavrsni&id=" . $file[$lastRevisionId][id] ?>">Snimi</a>        
	<?php
					if (isUserAuthorOfFileForZavrsni($file[$lastRevisionId][id], $userid))
					{
	?>
           <a href="<?php echo $linkPrefix . "&subaction=edit&id=" . $file[$firstRevisionId]['id'] ?>">Uredi</a>
           <a href="<?php echo $linkPrefix . "&subaction=del&id=" . $file[$firstRevisionId]['id']?>">Briši</a>
	<?php
					} //if user is author of this item

		?>        </td><!--options-->
    </tr><!--file_leading-->
    <?php
					if (count($file) > 1)
					{
						
						for ($i = 1; $i < count($file); $i++)
						{	
							$revision = $file[$i];
							$authorZavrsni = getAuthorOfFileForZavrsni($revision[id]);
	?>
            <tr class="file_<?=$file[$lastRevisionId][id] ?>_revisions" style="display: none;" id="file_revisions">
                <td class="creation_date"><?=date('d.m.Y H:i:s', mysql2time($revision[vrijeme]))?></td><!--vrijeme-->
                <td class="author"><?=filtered_output_string($author['ime'] . ' ' . $author['prezime']) ?></td><!--author-->
                <td class="revision">v<?=$revision[revizija] ?></td><!--revizija-->
                <td class="filename"><?=filtered_output_string($revision[filename]) ?></td><!--filename-->
                <td class="filesize"><?php
                    $lokacijafajlova ="$conf_files_path/zavrsni/fajlovi/$zavrsni/" . $revision[osoba] . "/" . 
                    $revision[filename] . '/v' . $revision[revizija] . '/';
                    $filepath = $lokacijafajlova . $revision[filename];
                    $filesize = filesize($filepath);
                    echo nicesize($filesize);
                    ?>
                </td><!--filesize-->
                <td class="options">
                    <a href="<?='index.php?sta=common/attachment' . "&tip=zavrsni&zavrsni=$zavrsni&id=" . $revision[id] ?>">Snimi</a>        
                </td><!--options-->
            </tr><!--file_revision-->	
    <?php					
						} //foreach revision

					} //if count files > 1

				} //foreach file
	?>
    </table>
<!--files_table-->
<?php
				$numrowsZavrsni = getCountFilesForZavrsniWithoutRevisions($zavrsni1[id]);
							
				$maxPage = ceil($numrows/$rowsPerPage);
				$self = $linkPrefix;
			
				if ($maxPage > 0)
				{
					echo "<span class=\"newsPages\">";
					if ($pageNum > 1)
					{
						$page = $pageNum - 1;
						$prev = " <a href=\"$self&page=$page\">[Prethodna]</a> ";
						
						$first = " <a href=\"$self&page=1\">[Prva]</a> ";
					} 
					
					if ($pageNum < $maxPage)
					{
						$page = $pageNum + 1;
						$next = " <a href=\"$self&page=$page\">[Sljedeća]</a> ";
						
						$last = " <a href=\"$self&page=$maxPage\">[Zadnja]</a> ";
					} 
					
					echo $first . $prev . " Strana <strong>$pageNum</strong> od ukupno <strong>$maxPage</strong> " . $next . $last;
					echo "</span>"; //newsPages span
				}	
				
	?>
    <script type="text/javascript">
		function getElementsByClassName( strClassName, obj ) 
		{
			var ar = arguments[2] || new Array();
			var re = new RegExp("\\b" + strClassName + "\\b", "g");
		
			if ( re.test(obj.className) ) 
			{
				ar.push( obj );
			}
			for ( var i = 0; i < obj.childNodes.length; i++ )
				getElementsByClassName( strClassName, obj.childNodes[i], ar );
			
			return ar;
		}
		
		function toggleFileRevisions(divID)
		{
			 var aryClassElements = getElementsByClassName( divID, document.body );
			for ( var i = 0; i < aryClassElements.length; i++ ) 
			{
				if (aryClassElements[i].style.display == '')
					aryClassElements[i].style.display = 'none';
				else
					aryClassElements[i].style.display = '';	
			}
		}
	
	</script>
    <?php		
			} //subaction not set
			else
			{
				if ($subaction == 'add')
				{
					
					if (!isset($_REQUEST['submit']))
					{
				
	?>
						 <h3>Novi fajl</h3>
				<?php
					print genform("POST", "addForm\" enctype=\"multipart/form-data\" ");
				?>
						
						<div id="formDiv">
							Polja sa * su obavezna. <br />
							<b>Limit za upload je 20MB.</b> <br />
                            <div class="row">
                                <span class="label">Fajl *</span>
                                <span class="formw">
                                    <input name="filename" type="file" id="filename" size="60" />
                                    <input type="hidden" name="MAX_FILE_SIZE" value="20971520">
                                </span>
                            </div> 
                            
							<div class="row">	
								<span class="formw" style="margin-left:150px;"><input name="submit" type="submit" id="submit" value="Potvrdi"/></span>
							</div>
						
						</div><!--formDiv-->
						</form>
							
	<?php	
					} //not submitted yet
					else
					{
						$errorText = formProcess_file('add');
						if($errorText == '')
						{
							nicemessage('Novi fajl uspješno dodan.');
							zamgerlog("dodao novi fajl na temu zavrsnig rada $zavrsni (pp$predmet)", 2);
							$link = $linkPrefix;
						}
						else
						{	
							niceerror($errorText);
							$link = "javascript:history.back();";		
						}
						nicemessage('<a href="'. $link .'">Povratak.</a>');
					
					
					} //submitted the form
				
				} //subaction == add
				elseif ($subaction == 'edit')
				{
					if (!isUserAuthorOfFileForZavrsni($id, $userid))
						return;

					if (!isThisFileFirstRevision($id))
						return;
					
					//edit item
					if (!isset($_REQUEST['submit']))
					{
						$entryZavrsni = getFileFirstRevisionZavrsni($id);
						$lastRevisionEntryZavrsni = getFileLastRevisionZavrsni($id);
		?>
					 <h3>Uredi fajl</h3>
				<?php
					print genform("POST", "editForm\" enctype=\"multipart/form-data\" ");
				?>
					
					<div id="formDiv">
						Polja sa * su obavezna. <br />
						<b>Limit za upload je 20MB.</b> <br />							
					   <div class="row">
							<span class="label">Trenutni fajl</span>
							<span class="formw"><a href="<?='index.php?sta=common/attachment' . "&tip=zavrsni&zavrsni=$zavrsni&id=" . $lastRevisionEntryZavrsni[id]?>" >
								<?=filtered_output_string($lastRevisionEntryZavrsni[filename]) ?>
							</a>
							</span>
					   </div> 

						<div class="row">
						  <span class="label">Zamijeni fajl</span>
							<span class="formw">
								<input name="filename" type="file" id="filename" size="50" />
								<input type="hidden" name="MAX_FILE_SIZE" value="20971520">
							</span>
						</div>                         
						<div class="row">	
							<span class="formw" style="margin-left:150px;"><input name="submit" type="submit" id="submit" value="Potvrdi"/></span>
						</div>
					
					</div><!--formDiv-->
					</form>
	
		<?php				
								
					}
					else
					{
						$errorText = formProcess_file('edit');
						if($errorText == '')
						{
							nicemessage('Uspješno ste uredili fajl.');
							zamgerlog("uredio fajl na temi završnog rada $zavrsni (pp$predmet)", 2);
							$link = $linkPrefix;
						}
						else
						{	
							//an error occured trying to process the form
							niceerror($errorText);
							$link = "javascript:history.back();";	
							
						}
						nicemessage('<a href="'. $link .'">Povratak.</a>');
						
					} //submitted the form
					
				} //subaction == edit
				elseif ($subaction == 'del')
				{
					if (!isUserAuthorOfFileForZavrsni($id, $userid))
						return;
						
					if (!isThisFileFirstRevisionZavrsni($id))
						return;

					//delete item
					if (isset($id) && is_int($id) && $id > 0)
					{
						if (!isset($_REQUEST['c']))
						{
							echo "Da li ste sigurni da zelite obrisati ovaj fajl? Obrisacete sve revizije fajla sa servera.<br />";	
							echo '<a href="' . $linkPrefix . '&subaction=del&id=' . $id . '&c=true">Da</a> | <a href="' . $linkPrefix . '">Odustani</a>';			
						}
						else
						{
							if ($_REQUEST['c'] == 'true')
							{
								//delete the record
								if (deleteFileZavrsni($id))
								{
									nicemessage('Uspješno ste obrisali fajl.');	
									zamgerlog("obrisao fajl na temi zavrsnog rada $zavrsni (pp$predmet)", 2);
									$link = $linkPrefix;
								}
								else
								{
									niceerror('Doslo je do greske prilikom brisanja fajla. Molimo kontaktirajte administratora.');
									$link = "javascript:history.back();";	
								}
								nicemessage('<a href="'. $link .'">Povratak.</a>');
							}
							
						} //else isset c get parameter
								
						
					} //id is okay		
				
				} //subaction == del
	
			} //subaction set
			
		} //section == file
		elseif ($section == 'bb')
		{
			//links management
			$linkPrefix .='&section=bb';
    ?>
<h2>Grupa za diskusiju</h2>
 <div class="links clearfix" id="bl">
    <ul>
        <li><a href="<?php echo $linkPrefix?>">Lista tema</a></li>
        <li><a href="<?php echo $linkPrefix . "&subaction=add"?>">Nova tema</a></li>
    </ul>   
</div>	
    <?php
			if (!isset($subaction))
			{
				
				$rowsPerPage = 20;
				$pageNum = 1;
				if(isset($_REQUEST['page']))
				{
					$pageNum = $_REQUEST['page'];
				}
				// counting the offset
				$offset = ($pageNum - 1) * $rowsPerPage;
				
				$threadsZavrsni = fetchThreadsForZavrsni($zavrsni1[id], $offset, $rowsPerPage);
				$numrowsZavrsni = getCountThreadsForZavrsni($zavrsni1[id]);

	?>
<div id="threadList">
	<div class="threadRow caption clearfix">
        <div class="threadInfo">
        	<div class="views">Pregleda</div><!--views-->
        	<div class="lastReply">Zadnji odgovor</div><!--lastReply-->
            <div class="replies">Odgovora</div><!--replies-->
        </div><!--threadInfo-->
    	<div class="title">Teme (<?=$numrows ?>)</div><!--title-->		
    </div><!--threadRow caption-->
    <?php
				foreach($threadsZavrsni as $key => $thread)
				{
	?>
	<div class="threadRow clearfix<?php if  ($key % 2) echo ' pattern'?>">
        <div class="threadInfo">
        	<div class="views"><?=intval($thread[pregleda]) ?></div><!--views-->
        	<div class="lastReply"><?=date('d.m.Y H:i:s', mysql2time($thread[zadnji_post][vrijeme])) ?><br /><?=filtered_output_string($thread[zadnji_post][osoba][prezime] . ' ' . $thread[zadnji_post][osoba][ime]) ?></div><!--lastReply-->
            <div class="replies"><?=intval($thread[broj_odgovora]) ?></div><!--replies-->
        </div><!--threadInfo-->
    	<div class="title"><a href="<?=$linkPrefix . "&subaction=view&tid=$thread[id]" ?>" title="<?php echo $thread['naslov'] ?>"><?=filtered_output_string($thread[naslov]) ?></a></div><!--title-->
        <div class="author"><?=filtered_output_string($thread[prvi_post][osoba][prezime] . ' ' . $thread[prvi_post][osoba][ime]) ?></div><!--author-->		
    </div><!--threadRow caption-->
    <?php
				} //foreach thread
	?>
</div><!--threadList-->
    <?php
							
				$maxPage = ceil($numrows/$rowsPerPage);
				$self = $linkPrefix;
				
				if ($maxPage > 0)
				{
					echo "<span class=\"newsPages\">";
					if ($pageNum > 1)
					{
						$page = $pageNum - 1;
						$prev = " <a href=\"$self&page=$page\">[Prethodna]</a> ";
						
						$first = " <a href=\"$self&page=1\">[Prva]</a> ";
					} 
					
					if ($pageNum < $maxPage)
					{
						$page = $pageNum + 1;
						$next = " <a href=\"$self&page=$page\">[Sljedeća]</a> ";
						
						$last = " <a href=\"$self&page=$maxPage\">[Zadnja]</a> ";
					} 
					
					echo $first . $prev . " Strana <strong>$pageNum</strong> od ukupno <strong>$maxPage</strong> " . $next . $last;
					echo "</span>"; //newsPages span	
				}
							
			} //subactin not set
			else
			{
				if ($subaction == 'view')
				{
					$tid = intval($_REQUEST[tid]);
					$thread = getThreadAndPosts($tid);
					if (empty($thread))
					{
						zamgerlog("strane zavrsnih radova: nepostojeci thread sa IDom $id, tema $zavrsni (pp$predmet, ag$ag)", 3);
						return;	
					}	
					incrementThreadViewCount($thread[id]);		
					
	?>
    <div id="fullThread">
    <?php
					foreach ($thread[posts] as $post)
					{
	?>				
		<div class="post"><a name="p<?=$post[id] ?>">
        	<div id="post_<?=$post[id]?>_header" class="header clearfix" onClick="toggleShowPost('post_<?=$post[id] ?>')">
                <div class="buttons">
                	<a href="<?=$linkPrefix . "&subaction=add&tid=$post[tema]&id=$post[id]"?>" title="Odgovori na ovaj post">Odgovori</a>
    <?php
		if (isUserAuthorOfPost($post[id], $userid) == true)
		{
	?>
    				| <a href="<?=$linkPrefix . "&subaction=edit&tid=$post[tema]&id=$post[id]"?>" title="Uredi vlastiti post">Uredi</a>
    				| <a href="<?=$linkPrefix . "&subaction=del&tid=$post[tema]&id=$post[id]"?>" title="Obriši vlastiti post">Obriši</a>		
    <?php
		}
	
	?>
                </div>
                <div class="maininfo">
                	<div class="date"><?=date('d.m.Y H:i:s', mysql2time($post[vrijeme])) ?></div>
                    <div class="author"><?=filtered_output_string($post[osoba][prezime] . ' ' . $post[osoba][ime]) ?></div> - 
                    <div class="title"><?=filtered_output_string($post[naslov]) ?></div>
                </div>
            </div><!--header-->
            <div class="text" id="post_<?=$post[id] ?>_text"><?=filtered_output_string($post[tekst]) ?></div><!--text-->

        </div><!--post-->				
					
	<?php			
					} //foreach post
	?>
    
    
    </div><!--fullThread-->
        <script type="text/javascript">
		function toggleShowPost(divID)
		{
			header = document.getElementById(divID + '_header');
			text = document.getElementById(divID + '_text');
			if (text.style.display == 'block' || text.style.display == '')
			{
				text.style.display = 'none';
				header.style.backgroundColor = '#F5F5F5';
				header.style.color = 'black';
			}
			else
			{
				text.style.display = 'block';
				header.style.backgroundColor = '#EEEEEE';
			}	
				
		}
	
		</script>
	
    <?php
				
				
				
				} //subaction == view (thread)
				elseif ($subaction == 'add')
				{
		
					$threadID = intval($_REQUEST['tid']);
					
					if ($threadID <=0)
						$thread = false;
					else
						$thread = true;
					
					if ($thread == true)
					{
						$postInfo = getPostInfoForThread($threadID, $id);
						$extendedThreadInfo = array();
						getExtendedInfoForThread($threadID, $extendedThreadInfo);
						
						if (empty($postInfo))
						{
							zamgerlog("strane zavrsnih radova: odgovor na nepostojeci post $id, tema $zavrsni (pp$predmet)", 3);
							return;
						}	
					}
					if (!isset($_REQUEST['submit']))
					{
		
	?>	
    		
				 <h3><?php if ($thread == true) echo 'Novi odgovor'; else echo 'Nova tema'; ?></h3>
				<?php
					print genform("POST", "addForm");
				?>
                <?php 
					if ($thread == true)
					{
				?> 
					<input type="hidden" name="tid" value="<?=$threadID?>"  />
				<?php
					}
				?>
                <div id="formDiv">
                	Polja sa * su obavezna. <br />
                
                	<div class="row">
                        <span class="label">Naslov *</span>
                        <span class="formw"><input name="naslov" type="text" id="naslov" size="70" <?php if ($thread == true) {?> value="RE: <?=$extendedThreadInfo['naslov']?>"<?php } ?>/></span> 
                  	</div>
                    <div class="row">
                        <span class="label">Tekst *</span>
                        <span class="formw"><textarea name="tekst" cols="60" rows="15" wrap="physical" id="tekst"></textarea></span>
                   	</div> 
                                      
                    <div class="row">	
                      	<span class="formw" style="margin-left:150px;"><input name="submit" type="submit" id="submit" value="Potvrdi"/></span>
                    </div>
                
                </div><!--formDiv-->
               
                </form>
               
	<?php	
					} //not submitted yet
					else
					{
						$errorText = formProcess_bb('add', $thread, $threadID);
						if($errorText == '')
						{
							if ($thread == true)
							{
								nicemessage('Novi odgovor uspješno dodan.');
								zamgerlog("dodao novi odgovor na diskusiju ID $threadID, tema zavrsnog rada $zavrsni (pp$predmet)", 2);
							}
							else
							{
								nicemessage('Nova tema uspješno dodana.');
								zamgerlog("dodao novu temu na zavrsni rad $zavrsni (pp$predmet)", 2);
							}
								
							if (!empty($_REQUEST[tid]))				
								$link = $linkPrefix . "&subaction=view&tid=$_REQUEST[tid]";	
							else
								$link = $linkPrefix;
						}
						else
						{	
							niceerror($errorText);
							$link = "javascript:history.back();";		
						}
						nicemessage('<a href="'. $link .'">Povratak.</a>');
					
					
					} //submitted the form
	
				} //subaction == addThread
				elseif ($subaction == 'edit')
				{
					//edit item
					if (isUserAuthorOfPost($id, $userid) == false)
					{
						zamgerlog("pokusava urediti post $id a nije autor, tema zavrsnog rada $zavrsni (pp$predmet)", 3);
						return;
					}
					$threadID = intval($_REQUEST[tid]);
					if ($threadID <=0)
					{
						zamgerlog("pokusava urediti nepostojeci post $id, tema zavrsnog rada $zavrsni (pp$predmet)", 3);
						return;
					}
					
					
					if (!isset($_REQUEST['submit']))
					{
						$entry = getPost($id);
						if (empty($entry))
						{
							zamgerlog("pokusava urediti nepostojeci post $id, tema zavrsnog rada $zavrsni (pp$predmet)", 3);
							return;
						}
?>
				 <h3>Uredi post</h3>
				<?php
					print genform("POST", "editForm");
				?>
				<div id="formDiv">
					Polja sa * su obavezna. <br />
				
					<div class="row">
						<span class="label">Naslov *</span>
						<span class="formw"><input name="naslov" type="text" id="naslov" size="70" value="<?php echo $entry['naslov']?>" /></span> 
					</div>
					<div class="row">
						<span class="label">Tekst *</span>
						<span class="formw"><textarea name="tekst" cols="60" rows="15" wrap="physical" id="tekst"><?php echo $entry['tekst'] ?></textarea></span>
					</div> 
					
					<div class="row">	
						<span class="formw" style="margin-left:150px;"><input name="submit" type="submit" id="submit" value="Potvrdi"/></span>
					</div>
				
				</div><!--formDiv-->
				
				
				</form>
				
				
<?php				
						
					}
					else
					{
						$errorText = formProcess_bb('edit', $thread, $threadID);
						if($errorText == '')
						{
							nicemessage('Uspješno ste uredili post.');
							zamgerlog("uredio vlastiti BB post $id, tema zavrsnog rada $zavrsni (pp$predmet)", 2);
							$link = $linkPrefix . "&subaction=view&tid=$_REQUEST[tid]";
						}
						else
						{	
							//an error occured trying to process the form
							niceerror($errorText);
							$link = "javascript:history.back();";	
							
						}
						nicemessage('<a href="'. $link .'">Povratak.</a>');
						
					} //submitted the form
				
				} //subaction == edit
				elseif ($subaction == 'del')
				{
					//delete item
					if (isset($id) && is_int($id) && $id > 0)
					{
						if (isUserAuthorOfPost($id, $userid) == false)
						{
							zamgerlog("pokusava izbrisati post $id a nije autor, tema zavrsnog rada $zavrsni (pp$predmet)", 3);
							return;
						}
						$threadID = intval($_REQUEST[tid]);
						if ($threadID<=0)
						{
							zamgerlog("pokusava izbrisati nepostojeci post $id, tema zavrsnog rada $zavrsni (pp$predmet)", 3);
							return;
						}
						
						if (!isset($_REQUEST['c']))
						{
							echo "Da li ste sigurni da zelite obrisati ovaj post? <br />";	
							echo '<a href="' . $linkPrefix .'&amp;subaction=del&tid=' . $threadID .'&id=' . $id . '&amp;c=true">Da</a> | <a href="' . $linkPrefix . '">Odustani</a>';			
						}
						else
						{
							if ($_REQUEST['c'] == 'true')
							{
								//delete the record
								if (deletePostZavrsni($id))
								{
									nicemessage('Uspješno ste obrisali post.');	
									zamgerlog("obrisao post na temu zavrsnog rada $zavrsni (pp$predmet)", 2);
									if (getCountPostsInThread($threadID) > 0)
										$link = $linkPrefix . "&subaction=view&tid=$_REQUEST[tid]";	
									else
										$link = $linkPrefix;
								}
								else
								{
									niceerror('Došlo je do greske prilikom brisanja posta. Molimo kontaktirajte administratora.');
									$link = "javascript:history.back();";	
								}
								nicemessage('<a href="'. $link .'">Povratak.</a>');
							}
							
						 } //else isset c get parameter
								
						
					  } //id is okay		
				
				 } //subaction == del
		
			} //subaction set
				
		} //section == bb (forum)		
	
	} //else - section is set

} //function


function formProcess_links($option)
{
	$errorText = '';
	if (!check_csrf_token()) 
	{
		zamgerlog("csrf token nije dobar",3);
		return "Poslani podaci nisu ispravni. Vratite se nazad, ponovo popunite formu i kliknite na dugme Pošalji";
	}
	if (!in_array($option, array('add', 'edit') ) )
	{
		$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
		return $errorText;		
	}
	
	$id = intval($_REQUEST['id']);
	
	if ($option == 'edit' && $id <=0)
	{
		$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
		zamgerlog("pokusao urediti nepostojeci link $id, tema zavrsnog rada $zavrsni (pp$predmet)", 3);

		return $errorText;
	}
	
	//get variables
	$naziv 	= $_REQUEST['naziv'];
	$url 	= $_REQUEST['url'];
	$opis 	= $_REQUEST['opis'];
	
	$zavrsni = intval($_REQUEST['zavrsni']);
	$predmet = intval($_REQUEST['predmet']);
	global $userid;
	
	
	
	
	if (empty($naziv) || empty($url))
	{
		$errorText = 'Unesite sva obavezna polja.';
		return $errorText;
	}
	
	$naziv = trim($naziv);
	$url = trim($url);
	$opis = trim($opis);
		
	$data = array(
				'naziv' => $naziv, 
				'url' => $url, 
				'opis' => $opis, 
				'osoba' => $userid, 
				'zavrsni' => $zavrsni 
	);
	
	if ($option == 'add')
	{
		if (!insertLinkZavrsni($data))
		{
			$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
			return $errorText;		
		}
	
	} //option == add
	else
	{
		if (!updateLinkZavrsni($data, $id))
		{
			$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
			return $errorText;		
		}
	
	} //option == edit


	return $errorText;
	
}

function insertLinkZavrsni($data)
{

	//generate unique id value
	$id = generateIdFromTable('projekat_link');
	
	$query = sprintf("INSERT INTO zavrsni_link (id, naziv, url, opis, zavrsni, osoba) VALUES ('%d', '%s', '%s', '%s', '%d', '%d')", 
											$id, 
											my_escape($data['naziv']), 
											my_escape($data['url']), 
											my_escape($data['opis']), 
											intval($data['zavrsni']), 
											intval($data['osoba'])  
											
					);
	$result = myquery($query);	
	
	return ( $result == false ) ? false : true;
}

function updateLinkZavrsni($data, $id)
{
	$query = sprintf("UPDATE zavrsni_link SET naziv='%s', url='%s', opis='%s' WHERE id='%d' LIMIT 1", 
											my_escape($data['naziv']), 
											my_escape($data['url']), 
											my_escape($data['opis']),
											intval($id) 
											
					);
	$result = myquery($query);	

	return ( $result == false ) ? false : true;
}
function deleteLinkZavrsni($id)
{
	$query = sprintf("DELETE FROM zavrsni_link WHERE id='%d' LIMIT 1", 
					intval($id)
					);
	
	$result = myquery($query);
	
	return ( $result == false ) ? false : true;
}

function formProcess_rss($option)
{
	$errorText = '';
	if (!check_csrf_token()) 
	{
		zamgerlog("csrf token nije dobar",3);
		return "Poslani podaci nisu ispravni. Vratite se nazad, ponovo popunite formu i kliknite na dugme Pošalji";
	}
	if (!in_array($option, array('add', 'edit') ) )
	{
		$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
		return $errorText;		
	}
	
	$id = intval($_REQUEST['id']);
	
	if ($option == 'edit' && $id <=0)
	{
		$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
		zamgerlog("pokusao urediti nepostojeci rss feed $id, zavrsni rad $zavrsni (pp$predmet)", 3);
		return $errorText;
	}
	
	//get variables
	$naziv 	= $_REQUEST['naziv'];
	$url 	= $_REQUEST['url'];
	$opis 	= $_REQUEST['opis'];
	
	$zavrsni = intval($_REQUEST['zavrsni']);
	$predmet = intval($_REQUEST['predmet']);
	global $userid;
	
	
	
	
	if (empty($naziv) || empty($url))
	{
		$errorText = 'Unesite sva obavezna polja.';
		return $errorText;
	}
	
	$naziv = trim($naziv);
	$url = trim($url);
	$opis = trim($opis);
	
	$data = array(
				'naziv' => $naziv, 
				'url' => $url, 
				'opis' => $opis, 
				'osoba' => $userid, 
				'zavrsni' => $zavrsni 
	);
	
	if ($option == 'add')
	{
		if (!insertRSSZavrsni($data))
		{
			$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
			return $errorText;		
		}
	
	} //option == add
	else
	{
		if (!updateRSSZavrsni($data, $id))
		{
			$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
			return $errorText;		
		}
	
	} //option == edit


	return $errorText;
	
}

function insertRSSZavrsni($data)
{

	//generate unique id value
	$id = generateIdFromTable('projekat_rss');
	
	$query = sprintf("INSERT INTO zavrsni_rss (id, naziv, url, opis, zavrsni, osoba) VALUES ('%d', '%s', '%s', '%s', '%d', '%d')", 
											$id, 
											my_escape($data['naziv']), 
											my_escape($data['url']), 
											my_escape($data['opis']), 
											intval($data['zavrsni']), 
											intval($data['osoba'])  
											
					);
	$result = myquery($query);	
	
	return ( $result == false ) ? false : true;
}

function updateRSSZavrsni($data, $id)
{
	$query = sprintf("UPDATE zavrsni_RSS SET naziv='%s', url='%s', opis='%s' WHERE id='%d' LIMIT 1", 
											my_escape($data['naziv']), 
											my_escape($data['url']), 
											my_escape($data['opis']),
											intval($id) 
											
					);
	$result = myquery($query);	

	return ( $result == false ) ? false : true;
}
function deleteRSSZavrsni($id)
{
	$query = sprintf("DELETE FROM zavrsni_rss WHERE id='%d' LIMIT 1", 
					intval($id)
					);
	
	$result = myquery($query);
	
	return ( $result == false ) ? false : true;
}


function formProcess_bl($option)
{
	$errorText = '';
	if (!check_csrf_token()) 
	{
		zamgerlog("csrf token nije dobar",3);
		return "Poslani podaci nisu ispravni. Vratite se nazad, ponovo popunite formu i kliknite na dugme Pošalji";
	}
	if (!in_array($option, array('add', 'edit') ) )
	{
		$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
		return $errorText;		
	}
	
	$id = intval($_REQUEST['id']);
	
	if ($option == 'edit' && $id <=0)
	{
		$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
		zamgerlog("pokusao urediti nepostojeci clanak $id, zavrsni rad $zavrsni (pp$predmet)", 3);
		return $errorText;
	}
	
	//get variables
	$naslov 	= $_REQUEST['naslov'];
	$tekst 		= $_REQUEST['tekst'];
	$slika 		= $_FILES['image'];
	
	$zavrsni = intval($_REQUEST['zavrsni']);
	$predmet = intval($_REQUEST['predmet']);
	global $userid;

	
	if (empty($naslov))
	{
		$errorText = 'Unesite sva obavezna polja.';
		return $errorText;
	}
	
	$naslov = trim($naslov);
	$tekst = trim($tekst);
	
	
	//process image
	if ($option == 'edit')
	{
		$entryZavrsni = getArticle($id);
	}
	
	global $conf_files_path;
	$lokacijaclanaka ="$conf_files_path/zavrsni/clanci/$zavrsni/$userid/";
	
	if (!file_exists("$conf_files_path/zavrsni/clanci/$zavrsni")) 
	{
		mkdir ("$conf_files_path/zavrsni/clanci/$zavrsni",0777, true);
	}
	if (!file_exists($lokacijaclanaka)) 
	{
		mkdir ($lokacijaclanaka,0777, true);
	}


	if ($slika['error'] != 4)
	{

		//cannot delete original image and preplace it with the new image so check this also
		
		if (isset($_REQUEST['delete']))
		{
			$errorText .= 'Selektujte ili brisanje slike, ili zamjena slike, ne oboje!';
			return $errorText;
		}
		
		//adding or replacing image - depends on the $option parameter(add, edit)
		
		if ($slika['error'] > 0)
		{
			if ($slika['error'] == 1 || $slika['error'] == 2)
				$errorText .= 'Pokušavate poslati fajl koji je veci od dozvoljene velicine. Probajte sa manjim fajlom.<br />';
			else
				$errorText .= 'Vaš fajl nije poslan korektno. Molimo pokušajte ponovo.<br />';
			return $errorText;		
		}
		else
		{
			//No error occured so far
			
			$uploadDir = $lokacijaclanaka;
			
			# Go to all lower case for consistency
			$imageName = strtolower($slika["name"]);
						
			$extension = preg_replace('/.+(\..*)$/', '$1', $imageName); 
			
			$safeExtensions = array(
									'.jpg',
									'.jpeg', 
									'.gif', 
									'.png'								
							  
			);  

			if (!in_array($extension, $safeExtensions)) 
			{
				 $errorText .= 'Format slike nije dozvoljen. <br />';
				 return $errorText;	
			}
			if (getimagesize($slika['tmp_name']) == false)
			{
				 $errorText .= 'Format slike nije dozvoljen. <br />';
				 return $errorText;		
			}
			
			//final file name
			if ($option == 'add')
			{
				$uniqueID = date('YmdHis', time());
				$uploadFile =  $uniqueID . "$userid" . $extension;	
			}
			else
			{
				if ($entry['slika'] == '')
				{
					$uniqueID = date('YmdHis', time());
					$uploadFile =  $uniqueID . "$userid" . $extension;	
				}	
				else
					$uploadFile = $entry['slika'];
				
			}
			
			
			if (move_uploaded_file($slika['tmp_name'], $uploadDir . $uploadFile))
			{
				//transfered a file to upload directory from temp dir
				//if edit option REPLACING the old image (overwrite)
				chmod($uploadDir . $uploadFile, 0777);	
			
			} 
			else
			{
				$errorText .= 'Desila se greška prilikom uploada slike. Molimo kontaktirajte administratora.<br />';
				return $errorText;			
			} //else
			
		} //else
	
	
	} //if ($_FILES['slika']['error'] != 4)
	
	if ($option == 'add')
	{		
		if ($slika['error'] != 4)
			$imageURL  = $uploadFile;
		else	
			$imageURL  = '';
			
	} //add option
	else	
	//edit option
	{			
		if ($entry['slika'] == '')
		{
			$imageURL = $uploadFile;				
		}	
		else
		{
			if (isset($_REQUEST['delete']))
			{
				//delete image from server
				
				unlink($lokacijaclanaka . $entry['slika']);
				//reset image in the database
				$imageURL = '';
				
			}
			else
				$imageURL = $entry['slika'];
		}	
	}
	
	
	
	
	$data = array(
				'naslov' => $naslov, 
				'tekst' => $tekst, 
				'slika' => $imageURL, 
				'osoba' => $userid, 
				'zavrsni' => $zavrsni 
	);
	
	if ($option == 'add')
	{
		if (!insertArticleZavrsni($data))
		{
			$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
			return $errorText;		
		}
	
	} //option == add
	else
	{
		if (!updateArticleZavrsni($data, $id))
		{
			$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
			return $errorText;		
		}
	
	} //option == edit


	return $errorText;
	
}

function insertArticleZavrsni($data)
{

	//generate unique id value
	$id = generateIdFromTable('bl_clanak');
	
	$query = sprintf("INSERT INTO bl_clanak (id, naslov, tekst, slika, zavrsni, osoba) VALUES ('%d', '%s', '%s', '%s', '%d', '%d')", 
											$id, 
											my_escape($data['naslov']), 
											my_escape($data['tekst']), 
											my_escape($data['slika']), 
											intval($data['zavrsni']), 
											intval($data['osoba'])  
											
					);
	$result = myquery($query);	
	
	return ( $result == false ) ? false : true;
}

function updateArticleZavrsni($data, $id)
{
	$query = sprintf("UPDATE bl_clanak SET naslov='%s', tekst='%s', slika='%s' WHERE id='%d' LIMIT 1", 
											my_escape($data['naslov']), 
											my_escape($data['tekst']), 
											my_escape($data['slika']),
											intval($id) 
											
					);
	$result = myquery($query);	

	return ( $result == false ) ? false : true;
}
function deleteArticleZavrsni($id)
{	
	global $conf_files_path;
	
	$entry = getArticleZavrsni($id);
	$query = sprintf("DELETE FROM bl_clanak WHERE id='%d' LIMIT 1", 
					intval($id)
					);
	
	$result = myquery($query);
	
	if (mysql_affected_rows() == 0)
		return false;
	
	if ($entry[slika] != '')
	{
		$lokacijaclanaka ="$conf_files_path/zavrsni/clanci/" . $entry['zavrsni'] . '/' . $entry['osoba'] . '/';
		if (!unlink($lokacijaclanaka . $entry['slika']))
			return false;	
	}
	
	return true;
}

function formProcess_file($option)
{
	$errorText = '';
	if (!check_csrf_token()) 
	{
		zamgerlog("csrf token nije dobar",3);
		return "Poslani podaci nisu ispravni. Vratite se nazad, ponovo popunite formu i kliknite na dugme Pošalji";
	}
	
	set_time_limit(0);
	
	if (!in_array($option, array('add', 'edit') ) )
	{
		$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
		return $errorText;		
	}
	
	$id = intval($_REQUEST['id']);
	
	if ($option == 'edit' && $id <=0)
	{
		$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
		zamgerlog("pokusao urediti nepostojeci fajl $id, zavrsni rad $zavrsni (pp$predmet)", 3);
		return $errorText;
	}
	if ($option == 'edit' && isThisFileFirstRevision($id) == false)
	{
		//cannot get access to revisions other than the first one	
		$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
		zamgerlog("pokusao urediti staru reviziju fajla $id, zavrsni rad $zavrsni (pp$predmet)", 3);
		return $errorText;
	}
	
	//process file
	if ($option == 'edit')
	{
		$entryZavrsni = getFileFirstRevisionZavrsni($id);
		$lastRevisionEntryZavrsni = getFileLastRevisionZavrsni($id);
	}
	
	//get variables
	$filename	= $_FILES['filename'];
	
	if ($option == 'edit')
	{
		$revizija = $lastRevisionEntryZavrsni[revizija] + 1;
		$file = $entry['id'];
	}
	else
	{
		$revizija = 1;
		$file = '';	
	}

	$zavrsni = intval($_REQUEST['zavrsni']);
	$predmet = intval($_REQUEST['predmet']);
	global $userid;

	
	if ($filename['error'] == 4)
	{
		$errorText = 'Unesite sva obavezna polja.';
		return $errorText;
	}
		

	global $conf_files_path;
	$lokacijafajlova ="$conf_files_path/zavrsni/fajlovi/$zavrsni/$userid/";
	
	if (!file_exists("$conf_files_path/zavrsni/fajlovi/$zavrsni")) 
	{
		mkdir ("$conf_files_path/zavrsni/fajlovi/$zavrsni",0777, true);
	}
	if (!file_exists($lokacijafajlova)) 
	{
		mkdir ($lokacijafajlova,0777, true);
	}
	



	//adding or replacing file - depends on the $option parameter(add, edit)

	if ($filename['error'] > 0)
	{
		if ($filename['error'] == 1 || $filename['error'] == 2)
			$errorText .= 'Pokušavate poslati fajl koji je veci od dozvoljene velicine. Probajte sa manjim fajlom.<br />';
		else
			$errorText .= 'Vaš fajl nije poslan korektno. Molimo pokušajte ponovo.<br />';
		return $errorText;		
	}
	else
	{
		//No error occured so far
		//escape file name before upload
		if ($option == 'add')
			$Name = $filename['name'];
		else
			$Name = $entry[filename];

		$Name = trim($Name);					
		
		//make directory structure for this file
		//$uploadDir = $lokacijafajlova . "$Name" . "_revizije/v$revizija/";
		$uploadDir = $lokacijafajlova . "$Name/";
		
		if (!file_exists($uploadDir)) 
		{
			mkdir ($uploadDir,0777, true);
		}
		$uploadDir = $uploadDir . "v$revizija/";
		
		if (!file_exists($uploadDir)) 
		{
			mkdir ($uploadDir,0777, true);
		}


		//final file name
		if ($option == 'add')
			$uploadFile =  $Name;
		else
			$uploadFile = $entry['filename'];

		
		if (move_uploaded_file($filename['tmp_name'], $uploadDir . $uploadFile))
		{
			//transfered a file to upload directory from temp dir
			//if edit option REPLACING the old image (overwrite)
			chmod($uploadDir . $uploadFile, 0777);	
		} 
		else
		{
			
			$errorText .= 'Desila se greška prilikom uploada fajla. Molimo kontaktirajte administratora.<br />AA';
			return $errorText;			
		} //else
		
	} //else
	
	//diff
	$diff = '';
	$diffing = 0;

	if ($option == 'edit')
	{
		//diffing with textual files only
		$lastRevisionFile = $lokacijafajlova . $lastRevisionEntry['filename'] . '/v' . $lastRevisionEntry['revizija'] . '/' . $lastRevisionEntry['filename'];
		$newFile          = $uploadDir . $uploadFile;
		
		$extension = preg_replace('/.+(\..*)$/', '$1', $lastRevisionEntry['filename']);
		$textExtensions = array(
								'.txt'
								);  

		if (in_array($extension, $textExtensions)) 
			$diffing = 1;
		
		if ($diffing == 1)
		{
			$diff = `/usr/bin/diff -u $lastRevisionFile $newFile`;
		}	
		 
	} //option == edit

	
	$data = array(
				'filename' => $uploadFile,
				'revizija' => $revizija, 
				'file' => $file, 
				'osoba' => $userid, 
				'zavrsni' => $zavrsni, 
				'diffing' => $diffing, 
				'diff' => $diff
	);
	
	if (!insertFileZavrsni($data))
	{
		$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
		return $errorText;		
	}
	
	return $errorText;
	
}

function insertFileZavrsni($data)
{

	//generate unique id value
	$id = generateIdFromTable('projekat_file');
	
	$query = sprintf("INSERT INTO zavrsni_file (id, filename, revizija, osoba, zavrsni, file) VALUES ('%d', '%s', '%d', '%d', '%d', '%d')", 
											$id, 
											my_escape($data['filename']), 
											intval($data['revizija']), 
											intval($data['osoba']), 
											intval($data['zavrsni']), 
											intval($data['file'])  						
					);
	$result = myquery($query);	
	
	if ($result == false || mysql_affected_rows() == 0)
		return false;
	
	//insert diff
	if ($data['diffing'] == 1)
	{
		$query = sprintf("INSERT INTO zavrsni_file_diff (file, diff) VALUES ('%d', '%s')", 
											$id, 
											my_escape($data['diff'])
		);
		$result = myquery($query);
		
		if ($result == false || mysql_affected_rows() == 0)
			return false;			
	}
	
	return true;	
}

function deleteFileZavrsni($id)
{
	global $conf_files_path;
	
	$listZavrsni = fetchAllRevisionsForFileZavrsni($id);
	
	foreach ($listZavrsni as $item)
	{
		$query = sprintf("DELETE FROM zavrsni_file WHERE id='%d' LIMIT 1", 
					intval($item[id])
					);
	
		$result = myquery($query);
		if (mysql_affected_rows() == 0)
			return false;
			
		$lokacijarevizije = "$conf_files_path/zavrsni/fajlovi/" . $item['zavrsni'] . '/' . $item['osoba'] . '/' . $item['filename'] . '/v' . $item['revizija'];
		
		if (!unlink($lokacijarevizije . '/' . $item[filename]))
			return false;	
		if (!rmdir($lokacijarevizije))
			return false;
			
		//remove any diffs for this file
		myquery("DELETE FROM zavrsni_file_diff WHERE file='" . $item[id] . "' LIMIT 1");
	}
	
	$lokacijafajlova = "$conf_files_path/zavrsni/fajlovi/" . $list[0]['zavrsni'] . '/' . $list[0]['osoba'] . '/' . $list[0]['filename'];
	if (!rmdir($lokacijafajlova))
		return false;
	
	return true;
}


function formProcess_bb($option, $thread, $threadID)
{
	$errorText = '';
	if (!check_csrf_token()) 
	{
		zamgerlog("csrf token nije dobar",3);
		return "Poslani podaci nisu ispravni. Vratite se nazad, ponovo popunite formu i kliknite na dugme Pošalji";
	}
	if (!in_array($option, array('add', 'edit') ) )
	{
		$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
		return $errorText;		
	}
	
	$id = intval($_REQUEST['id']);
	
	if ($option == 'edit' && $id <=0)
	{
		$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
		zamgerlog("pokusao urediti nepostojeci post $id, zavrsni rad $zavrsni (pp$predmet)", 3);
		return $errorText;
	}

	if ($thread == true && $threadID <=0)
	{
		$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
		zamgerlog("pokusao urediti nepostojeci post $id, zavrsni rad $zavrsni (pp$predmet)", 3);
		return $errorText;
	}
	
	
	//get variables
	$naslov 	= $_REQUEST['naslov'];
	$tekst 		= $_REQUEST['tekst'];
	
	$zavrsni = intval($_REQUEST['zavrsni']);
	$predmet = intval($_REQUEST['predmet']);
	global $userid;

	
	if (empty($naslov) || empty($tekst))
	{
		$errorText = 'Unesite sva obavezna polja.';
		return $errorText;
	}
	
	$naslov = trim($naslov);
	$tekst = trim($tekst);
	
	if ($option == 'edit')
	{
		$entry = getPost($id);
	}
	
	
	
	$data = array(
				'naslov' => $naslov, 
				'tekst' => $tekst, 
				'osoba' => $userid, 
				'zavrsni' => $zavrsni, 
				'threadID' => $threadID //only used in insertReply if thread == true		
	);
	
	if ($option == 'add')
	{
		if ($thread == false)
		{
			//new thread inserting
			if (!insertThread($data))
			{
				$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
				return $errorText;		
			}
		} //thread false
		else
		{
			//inserting post in thread
			if (!insertReplyForThread($threadID, $data))
			{
				$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
				return $errorText;		
			}
		
		}
	
	} //option == add
	else
	{
		if (!updatePost($data, $id))
		{
			$errorText = 'Doslo je do greske prilikom spasavanja podataka. Molimo kontaktirajte administratora.';
			return $errorText;		
		}
	
	} //option == edit


	return $errorText;
	
}

function insertThreadZavrsni($data)
{
	//generate unique id value
	$thread_id = generateIdFromTable('bb_tema');
	
	$query = sprintf("INSERT INTO bb_tema (id, osoba, zavrsni) VALUES('%d', '%d', '%d')", 
											$thread_id,
											intval($data['osoba']), 
											intval($data['zavrsni'])											
	
	);
	$result = myquery($query);	
	
	if ($result == false || mysql_affected_rows() == 0)
		return false;
	$post_id = insertReplyForThread($thread_id, $data);
	if ($post_id == false)
		return false;
	

	//update some data in newly created thread
	$query = sprintf("UPDATE bb_tema SET prvi_post='%d', zadnji_post='%d' WHERE id='%d' LIMIT 1", 
											$post_id, 
											$post_id, 
											$thread_id
	);
	
	$result = myquery($query);
	
	if ($result == false || mysql_affected_rows() == 0)
		return false;
		
	return true;
}

function insertReplyForThread($thread, $data)
{
	
	//insert post for this thread, this is the first post
	$post_id = generateIdFromTable('bb_post');
	$query = sprintf("INSERT INTO bb_post (id, naslov, osoba, tema) VALUES('%d', '%s', '%d', '%d')", 
											$post_id, 
											my_escape($data['naslov']), 
											intval($data['osoba']), 
											$thread	
	);
	$result = myquery($query);
	if ($result == false || mysql_affected_rows() == 0)
		return false;
	
	//insert text for this post
	$query = sprintf("REPLACE INTO bb_post_text SET post='%d', tekst='%s'", 
											$post_id, 
											my_escape($data['tekst'])	
	);
	
	$result = myquery($query);
	if ($result == false || mysql_affected_rows() == 0)
		return false;
	
	//update some data in thread
	$query = sprintf("UPDATE bb_tema SET zadnji_post='%d' WHERE id='%d' LIMIT 1", 
											$post_id, 
											$thread
	);
	
	$result = myquery($query);
	
	if ($result == false || mysql_affected_rows() == 0)
		return false;
	
	return $post_id;	
}	

function updatePost($data, $id)
{
	$query = sprintf("UPDATE bb_post SET naslov='%s' WHERE id='%d' LIMIT 1", 
											my_escape($data['naslov']), 
											intval($id) 
											
					);
	$result = myquery($query);
	
	if ($result == false)
		return false;	
	
	$query = sprintf("UPDATE bb_post_text SET tekst='%s' WHERE post='%d' LIMIT 1", 
											my_escape($data['tekst']), 
											intval($id) 
											
					);
	$result = myquery($query);

	return ( $result == false ) ? false : true;
}

function deletePost($id)
{	
	$query = sprintf("DELETE FROM bb_post WHERE id='%d' LIMIT 1", 
					intval($id)
					);
	
	$result = myquery($query);
	
	if (mysql_affected_rows() == 0)
		return false;
		
	$query = sprintf("DELETE FROM bb_post_text WHERE post='%d' LIMIT 1", 
					intval($id)
					);
	
	$result = myquery($query);
	
	if (mysql_affected_rows() == 0)
		return false;
	
	//if first post, delete thread
	
	$result = myquery("SELECT prvi_post, id FROM bb_tema WHERE prvi_post='$id' LIMIT 1");
	
	if (mysql_num_rows($result) > 0)
	{
		//delete evetyhing
		$row = mysql_fetch_assoc($result);
		$thread = $row[id];
		
		$result = myquery("DELETE FROM bb_tema WHERE id='$thread' LIMIT 1");
		if ($result == false || mysql_affected_rows() == 0)
			return false;
			
		return true;
	}
	
	$result = myquery("SELECT zadnji_post, id FROM bb_tema WHERE zadnji_post='$id' LIMIT 1");
	if (mysql_num_rows($result) > 0)
	{
		//assign this value to the new last post
		$row = mysql_fetch_assoc($result);
		$thread = $row[id];
		
		$result = myquery("SELECT id FROM bb_post WHERE tema='$thread' ORDER BY vrijeme DESC LIMIT 1");
		$row = mysql_fetch_assoc($result);
		$post = $row[id];
		
		$result = myquery("UPDATE bb_tema SET zadnji_post='$post' WHERE id='$thread' LIMIT 1");
		if ($result == false || mysql_affected_rows() == 0)
			return false;
		
		return true;		
	}	

	return true;
}

?>