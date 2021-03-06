<div class="header" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html"
	 xmlns="http://www.w3.org/1999/html">
	<div class="breadcrumb-wrapper">
		<ol class="breadcrumb">
			<li><a href="{$smarty.const.WWW_TOP}{$site->home_link}">Home</a></li>
			/ {$catname|escape:"htmlall"}
		</ol>
	</div>
</div>
<div class="well well-sm">
	{include file='search-filter.tpl'}
</div>
<form id="nzb_multi_operations_form" action="get">
	<div class="box-body"
	<div class="row">
		<div class="col-xlg-12 portlets">
			<div class="panel panel-default">
				<div class="panel-body pagination2">
					<div class="row">
						<div class="col-md-8">
							<div class="nzb_multi_operations">
								View: <strong>Covers</strong> | <a
										href="{$smarty.const.WWW_TOP}/browse?t={$category}">List</a><br/>
								Check all: <input type="checkbox" class="square-all"/> <br/>
								With Selected:
								<div class="btn-group">
									<input type="button"
										   class="nzb_multi_operations_download btn btn-sm btn-success"
										   value="Download NZBs"/>
									<input type="button"
										   class="nzb_multi_operations_cart btn btn-sm btn-info"
										   value="Send to my Download Basket"/>
									{if isset($sabintegrated) && $sabintegrated !=""}
										<input type="button"
											   class="nzb_multi_operations_sab btn btn-sm btn-primary"
											   value="Send to Queue"/>
									{/if}
									{if isset($isadmin)}
										<input type="button"
											   class="nzb_multi_operations_edit btn btn-sm btn-warning"
											   value="Edit"/>
										<input type="button"
											   class="nzb_multi_operations_delete btn btn-sm btn-danger"
											   value="Delete"/>
									{/if}
								</div>
							</div>
						</div>
						<div class="col-md-4">
							{$pager}
						</div>
					</div>
					<hr>
					{foreach $results as $result}
						<!-- Iteratie: {counter} -->
						{if isset($result.category_name)}
							{assign var="catnamesplit" value=">"|explode:$result.category_name}
						{/if}
						{if $result@iteration is odd by 1}
							<!-- Begin Row -->
							<div class="row">
								<!-- Left -->
								<div class="col-md-6 small-gutter-right movie-height">
									<div class="panel panel-default">
										<div class="panel-body">
											<div class="row small-gutter-left">
												<div class="col-md-3 small-gutter-left">
													{assign var="msplits" value=","|explode:$result.grp_release_id}
													{assign var="mguid" value=","|explode:$result.grp_release_guid}
													{assign var="mnfo" value=","|explode:$result.grp_release_nfoid}
													{assign var="mgrp" value=","|explode:$result.grp_release_grpname}
													{assign var="mname" value="#"|explode:$result.grp_release_name}
													{assign var="mpostdate" value=","|explode:$result.grp_release_postdate}
													{assign var="msize" value=","|explode:$result.grp_release_size}
													{assign var="mtotalparts" value=","|explode:$result.grp_release_totalparts}
													{assign var="mcomments" value=","|explode:$result.grp_release_comments}
													{assign var="mgrabs" value=","|explode:$result.grp_release_grabs}
													{assign var="mfailed" value=","|explode:$result.grp_release_failed}
													{assign var="mpass" value=","|explode:$result.grp_release_password}
													{assign var="minnerfiles" value=","|explode:$result.grp_rarinnerfilecount}
													{assign var="mhaspreview" value=","|explode:$result.grp_haspreview}
													{foreach $msplits as $loop=>$m name="loop"}
													{if $smarty.foreach.loop.first}
													<a href="{$smarty.const.WWW_TOP}/xxx/?id={$result.id}"><img
																class="cover img-responsive img-rounded"
																src="{if $result.cover == 1}{$smarty.const.WWW_TOP}/covers/xxx/{$result.id}-cover.jpg{else}{$smarty.const.WWW_THEMES}/shared/images/no-cover.png{/if}"
																width="100" border="0"
																alt="{$result.title|escape:"htmlall"}"/>{if !empty($mfailed[$m@index])}
														<i class="fa fa-exclamation-circle" style="color: red"
														   title="This release has failed to download for some users"></i>{/if}
													</a>
													{if $result.classused == "ade"}
														<a
																target="_blank"
																href="{$site->dereferrer_link}{$result.directurl}"
																name="viewade{$result.title}"
																title="View AdultdvdEmpire page"
																><img
																	src="{$smarty.const.WWW_THEMES}/shared/images/icons/ade.png"></a>
													{else}
														<a
																target="_blank"
																href="{$site->dereferrer_link}http://www.adultdvdempire.com/dvd/search?q={$result.title}"
																name="viewade{$result.title}"
																title="Search AdultdvdEmpire page"
																><img
																	src="{$smarty.const.WWW_THEMES}/shared/images/icons/ade.png"></a>
													{/if}
													{if $result.classused == "hm"}
														<a
																target="_blank"
																href="{$site->dereferrer_link}{$result.directurl}"
																name="viewhm{$result.title}"
																title="View Hot Movies page"
																><img
																	src="{$smarty.const.WWW_THEMES}/shared/images/icons/hotmovies.png"></a>
													{else}
														<a
																target="_blank"
																href="{$site->dereferrer_link}http://www.hotmovies.com/search.php?words={$result.title}&complete=on&search_in=video_title"
																name="viewhm{$result.title}"
																title="Search Hot Movies page"
																><img
																	src="{$smarty.const.WWW_THEMES}/shared/images/icons/hotmovies.png"></a>
													{/if}
													{if $result.classused == "pop"}
														<a
																target="_blank"
																href="{$site->dereferrer_link}{$result.directurl}"
																name="viewpop{$result.id}"
																title="View Popporn page"
																><img
																	src="{$smarty.const.WWW_THEMES}/shared/images/icons/popporn.png"></a>
													{else}
														<a
																target="_blank"
																href="{$site->dereferrer_link}http://www.popporn.com/results/index.cfm?v=4&g=0&searchtext={$result.title}"
																name="viewpop{$result.id}"
																title="Search Popporn page"
																><img
																	src="{$smarty.const.WWW_THEMES}/shared/images/icons/popporn.png"></a>
													{/if}
													<a
															target="_blank"
															href="{$site->dereferrer_link}http://www.iafd.com/results.asp?searchtype=title&searchstring={$result.title}"
															name="viewiafd{$result.title}"
															title="Search Internet Adult Film Database"
															><img
																src="{$smarty.const.WWW_THEMES}/shared/images/icons/iafd.png"></a>
													{if $mnfo[$m@index] > 0}<a
														href="{$smarty.const.WWW_TOP}/nfo/{$mguid[$m@index]}"
														title="View NFO" class="modal_nfo label label-default"
														rel="nfo">NFO</a>{/if}
													<a class="label label-default"
													   href="{$smarty.const.WWW_TOP}/browse?g={$result.grp_release_grpname}"
													   title="Browse releases in {$result.grp_release_grpname|replace:"alt.binaries":"a.b"}">Group</a>
													{if !empty($mfailed[$m@index])}
														<span class="btn btn-default btn-xs" title="This release has failed to download for some users">
															<i class="fa fa-thumbs-o-up"></i> {$mgrabs[$m@index]} Grab{if {$mgrabs[$m@index]} != 1}s{/if} / <i class="fa fa-thumbs-o-down"></i> {$mfailed[$m@index]}Failed Download{if {$mfailed[$m@index]} > 1}s{/if}</span>
													{/if}
												</div>
												<div class="col-md-9 small-gutter-left table-responsive">
																<span class="release-title"><a class="text-muted"
																							   href="{$smarty.const.WWW_TOP}/xxx/?id={$result.id}">{$result.title|escape:"htmlall"}</a></span>

													<div class="release-subtitle">{if $result.genre != ''}{$result.genre}, {/if}</div>
													<div id="guid{$mguid[$m@index]}">
														<label>
															<input type="checkbox"
																   class="square"
																   value="{$mguid[$m@index]}"
																   id="chksingle"/>
														</label>
														<span class="label label-primary">{if isset($catsplit[0])} {$catsplit[0]}{/if}</span>
														<span class="label label-danger">{if isset($catsplit[1])} {$catsplit[1]}{/if}</span>
														<span class="label label-default">{$msize[$m@index]|fsize_format:"MB"}</span>
																	<span class="label label-default">Posted {$mpostdate[$m@index]|timeago}
																		ago</span>
														<br/><br/><br/>

														<div class="release-name text-muted"><a
																	href="{$smarty.const.WWW_TOP}/details/{$mguid[$m@index]}">{$mname[$m@index]|escape:"htmlall"}</a>
														</div>
														<div>
															<a role="button" class="btn btn-default btn-xs"
															   href="{$smarty.const.WWW_TOP}/getnzb/{$mguid[$m@index]}"><i
																		class="fa fa-cloud-download"></i><span
																		class="badge"> {$mgrabs[$m@index]}
																	Grab{if $mgrabs[$m@index] != 1}s{/if}</span></a>
															<a role="button" class="btn btn-default btn-xs"
															   href="{$smarty.const.WWW_TOP}/details/{$mguid[$m@index]}/#comments"><i
																		class="fa fa-comment-o"></i><span
																		class="badge"> {$mcomments[$m@index]}
																	Comment{if $mcomments[$m@index] != 1}s{/if}</span></a>
															<span
																	id="guid{$mguid[$m@index]}"
																	class="btn btn-hover btn-default btn-xs icon_cart text-muted"
																	title="Send to my Download Basket">
																	<i class="fa fa-shopping-basket"></i>
															</span>
															{if isset($sabintegrated) && $sabintegrated !=""}
																<span
																		id="guid{$mguid[$m@index]}"
																		class="btn btn-hover btn-default btn-xs icon_sab text-muted"
																		title="Send to my Queue">
																		<i class="fa fa-share"></i>
																</span>
															{/if}
															{if !empty($mfailed[$m@index])}
																<span class="btn btn-default btn-xs" title="This release has failed to download for some users">
																	<i class ="fa fa-thumbs-o-up"></i> {$mgrabs[$m@index]} Grab{if {$mgrabs[$m@index]} != 1}s{/if} / <i class ="fa fa-thumbs-o-down"></i> {$mfailed[$m@index]} Failed Download{if {$mfailed[$m@index]} > 1}s{/if}</span>															{/if}
														</div>
													</div>
													{/if}
													{/foreach}
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- /Left -->
								{else}
								<!-- Right -->
								<div class="col-md-6 small-gutter-left movie-height">
									<div class="panel panel-default">
										<div class="panel-body">
											<div class="row small-gutter-left">
												<div class="col-md-3 small-gutter-left">
													{assign var="msplits" value=","|explode:$result.grp_release_id}
													{assign var="mguid" value=","|explode:$result.grp_release_guid}
													{assign var="mnfo" value=","|explode:$result.grp_release_nfoid}
													{assign var="mgrp" value=","|explode:$result.grp_release_grpname}
													{assign var="mname" value="#"|explode:$result.grp_release_name}
													{assign var="mpostdate" value=","|explode:$result.grp_release_postdate}
													{assign var="msize" value=","|explode:$result.grp_release_size}
													{assign var="mtotalparts" value=","|explode:$result.grp_release_totalparts}
													{assign var="mcomments" value=","|explode:$result.grp_release_comments}
													{assign var="mgrabs" value=","|explode:$result.grp_release_grabs}
													{assign var="mfailed" value=","|explode:$result.grp_release_failed}
													{assign var="mpass" value=","|explode:$result.grp_release_password}
													{assign var="minnerfiles" value=","|explode:$result.grp_rarinnerfilecount}
													{assign var="mhaspreview" value=","|explode:$result.grp_haspreview}
													{foreach $msplits as $loop=>$m name="loop"}
													{if $smarty.foreach.loop.first}
													<a href="{$smarty.const.WWW_TOP}/xxx/?id={$result.id}"><img
																class="cover img-responsive img-rounded"
																src="{if $result.cover == 1}{$smarty.const.WWW_TOP}/covers/xxx/{$result.id}-cover.jpg{else}{$smarty.const.WWW_THEMES}/shared/images/no-cover.png{/if}"
																width="100" border="0"
																alt="{$result.title|escape:"htmlall"}"/>{if !empty($mfailed[$m@index])}
														<i class="fa fa-exclamation-circle" style="color: red"
														   title="This release has failed to download for some users"></i>{/if}
													</a>
													{if $result.classused == "ade"}
														<a
																target="_blank"
																href="{$site->dereferrer_link}{$result.directurl}"
																name="viewade{$result.title}"
																title="View AdultdvdEmpire page"
																><img
																	src="{$smarty.const.WWW_THEMES}/shared/images/icons/ade.png"></a>
													{else}
														<a
																target="_blank"
																href="{$site->dereferrer_link}http://www.adultdvdempire.com/dvd/search?q={$result.title}"
																name="viewade{$result.title}"
																title="Search AdultdvdEmpire page"
																><img
																	src="{$smarty.const.WWW_THEMES}/shared/images/icons/ade.png"></a>
													{/if}
													{if $result.classused == "hm"}
														<a
																target="_blank"
																href="{$site->dereferrer_link}{$result.directurl}"
																name="viewhm{$result.title}"
																title="View Hot Movies page"
																><img
																	src="{$smarty.const.WWW_THEMES}/shared/images/icons/hotmovies.png"></a>
													{else}
														<a
																target="_blank"
																href="{$site->dereferrer_link}http://www.hotmovies.com/search.php?words={$result.title}&complete=on&search_in=video_title"
																name="viewhm{$result.title}"
																title="Search Hot Movies page"
																><img
																	src="{$smarty.const.WWW_THEMES}/shared/images/icons/hotmovies.png"></a>
													{/if}
													{if $result.classused == "pop"}
														<a
																target="_blank"
																href="{$site->dereferrer_link}{$result.directurl}"
																name="viewpop{$result.id}"
																title="View Popporn page"
																><img
																	src="{$smarty.const.WWW_THEMES}/shared/images/icons/popporn.png"></a>
													{else}
														<a
																target="_blank"
																href="{$site->dereferrer_link}http://www.popporn.com/results/index.cfm?v=4&g=0&searchtext={$result.title}"
																name="viewpop{$result.id}"
																title="Search Popporn page"
																><img
																	src="{$smarty.const.WWW_THEMES}/shared/images/icons/popporn.png"></a>
													{/if}
													<a
															target="_blank"
															href="{$site->dereferrer_link}http://www.iafd.com/results.asp?searchtype=title&searchstring={$result.title}"
															name="viewiafd{$result.title}"
															title="Search Internet Adult Film Database"
															><img
																src="{$smarty.const.WWW_THEMES}/shared/images/icons/iafd.png"></a>
													{if $mnfo[$m@index] > 0}<a
														href="{$smarty.const.WWW_TOP}/nfo/{$mguid[$m@index]}"
														title="View NFO" class="modal_nfo label label-default"
														rel="nfo">NFO</a>{/if}
													<a class="label label-default"
													   href="{$smarty.const.WWW_TOP}/browse?g={$result.grp_release_grpname}"
													   title="Browse releases in {$result.grp_release_grpname|replace:"alt.binaries":"a.b"}">Group</a>
													{if !empty($mfailed[$m@index])}
														<span class="btn btn-default btn-xs" title="This release has failed to download for some users">
															<i class="fa fa-thumbs-o-up"></i> {$mgrabs[$m@index]} Grab{if {$mgrabs[$m@index]} != 1}s{/if} / <i class="fa fa-thumbs-o-down"></i> {$mfailed[$m@index]}Failed Download{if {$mfailed[$m@index]} > 1}s{/if}</span>
													{/if}
												</div>
												<div class="col-md-9 small-gutter-left table-responsive">
																<span class="release-title"><a class="text-muted"
																							   href="{$smarty.const.WWW_TOP}/xxx/?id={$result.id}">{$result.title|escape:"htmlall"}</a></span>

													<div class="release-subtitle">{if $result.genre != ''}{$result.genre}, {/if}</div>
													<div id="guid{$mguid[$m@index]}">
														<label>
															<input type="checkbox"
																   class="square"
																   value="{$mguid[$m@index]}"
																   id="chksingle"/>
														</label>
														<span class="label label-primary">{if isset($catsplit[0])} {$catsplit[0]}{/if}</span>
														<span class="label label-danger">{if isset($catsplit[1])} {$catsplit[1]}{/if}</span>
														<span class="label label-default">{$msize[$m@index]|fsize_format:"MB"}</span>
																	<span class="label label-default">Posted {$mpostdate[$m@index]|timeago}
																		ago</span>
														<br/><br/><br/>

														<div class="release-name text-muted"><a
																	href="{$smarty.const.WWW_TOP}/details/{$mguid[$m@index]}">{$mname[$m@index]|escape:"htmlall"}</a>
														</div>
														<div>
															<a role="button" class="btn btn-default btn-xs"
															   href="{$smarty.const.WWW_TOP}/getnzb/{$mguid[$m@index]}"><i
																		class="fa fa-cloud-download"></i><span
																		class="badge"> {$mgrabs[$m@index]}
																	Grab{if $mgrabs[$m@index] != 1}s{/if}</span></a>
															<a role="button" class="btn btn-default btn-xs"
															   href="{$smarty.const.WWW_TOP}/details/{$mguid[$m@index]}/#comments"><i
																		class="fa fa-comment-o"></i><span
																		class="badge"> {$mcomments[$m@index]}
																	Comment{if $mcomments[$m@index] != 1}s{/if}</span></a>
															<span
																	id="guid{$mguid[$m@index]}"
																	class="btn btn-hover btn-default btn-xs icon_cart text-muted"
																	title="Send to my Download Basket">
																	<i class="fa fa-shopping-basket"></i>
															</span>
															{if isset($sabintegrated) && $sabintegrated !=""}
																<span
																		id="guid{$mguid[$m@index]}"
																		class="btn btn-hover btn-default btn-xs icon_sab text-muted"
																		title="Send to my Queue">
																		<i class="fa fa-share"></i>
																</span>
															{/if}
															{if !empty($mfailed[$m@index])}
																<span class="btn btn-default btn-xs" title="This release has failed to download for some users">
																	<i class ="fa fa-thumbs-o-up"></i> {$mgrabs[$m@index]} Grab{if {$mgrabs[$m@index]} != 1}s{/if} / <i class ="fa fa-thumbs-o-down"></i> {$mfailed[$m@index]} Failed Download{if {$mfailed[$m@index]} > 1}s{/if}</span>															{/if}
														</div>
													</div>
													{/if}
													{/foreach}
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- /Right -->
							</div>
							<hr>
							<!-- End Row -->
						{/if}
					{/foreach}
					<div class="row">
						<div class="col-md-8">
							<div class="nzb_multi_operations">
								View: <strong>Covers</strong> | <a
										href="{$smarty.const.WWW_TOP}/browse?t={$category}">List</a><br/>
								Check all: <input type="checkbox" class="square-all"/> <br/>
								With Selected:
								<div class="btn-group">
									<input type="button"
										   class="nzb_multi_operations_download btn btn-sm btn-success"
										   value="Download NZBs"/>
									<input type="button"
										   class="nzb_multi_operations_cart btn btn-sm btn-info"
										   value="Send to my Download Basket"/>
									{if isset($sabintegrated) && $sabintegrated !=""}
										<input type="button"
											   class="nzb_multi_operations_sab btn btn-sm btn-primary"
											   value="Send to Queue"/>
									{/if}
									{if isset($isadmin)}
										<input type="button"
											   class="nzb_multi_operations_edit btn btn-sm btn-warning"
											   value="Edit"/>
										<input type="button"
											   class="nzb_multi_operations_delete btn btn-sm btn-danger"
											   value="Delete"/>
									{/if}
								</div>
							</div>
						</div>
						<div class="col-md-4">
							{$pager}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
