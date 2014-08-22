<div id="menucontainer">
	<div id="menulink">
		<ul>
			{foreach from=$parentcatlist item=parentcat}
				{if $parentcat.ID == 1000 && $userdata.consoleview=="1"}
					<li><a title="Browse {$parentcat.title}"
						   href="{$smarty.const.WWW_TOP}/console">{$parentcat.title}</a>
						<ul>
							{foreach from=$parentcat.subcatlist item=subcat}
								<li><a title="Browse {$subcat.title}"
									   href="{$smarty.const.WWW_TOP}/console?t={$subcat.ID}">{$subcat.title}</a></li>
							{/foreach}
						</ul>
					</li>
				{elseif $parentcat.ID == 2000 && $userdata.movieview=="1"}
					<li><a title="Browse {$parentcat.title}"
						   href="{$smarty.const.WWW_TOP}/movies">{$parentcat.title}</a>
						<ul>
							{foreach from=$parentcat.subcatlist item=subcat}
								<li><a title="Browse {$subcat.title}"
									   href="{$smarty.const.WWW_TOP}/movies?t={$subcat.ID}">{$subcat.title}</a></li>
							{/foreach}
						</ul>
					</li>
				{elseif ($parentcat.ID == 3000 && $userdata.musicview=="1")}
					<li><a title="Browse {$parentcat.title}" href="{$smarty.const.WWW_TOP}/music">{$parentcat.title}</a>
						<ul>
							{foreach from=$parentcat.subcatlist item=subcat}
								{if $subcat.ID == 3030}
									<li><a title="Browse {$subcat.title}"
										   href="{$smarty.const.WWW_TOP}/browse?t={$subcat.ID}">{$subcat.title}</a></li>
								{else}
									<li><a title="Browse {$subcat.title}"
										   href="{$smarty.const.WWW_TOP}/music?t={$subcat.ID}">{$subcat.title}</a></li>
								{/if}
							{/foreach}
						</ul>
					</li>
				{elseif ($parentcat.ID == 4000 && $userdata.gameview=="1")}
					<li><a title="Browse {$parentcat.title}" href="{$smarty.const.WWW_TOP}/games">{$parentcat.title}</a>
						<ul>
							{foreach from=$parentcat.subcatlist item=subcat}
								{if $subcat.ID == 4050}
									<li><a title="Browse {$subcat.title}"
										   href="{$smarty.const.WWW_TOP}/games">{$subcat.title}</a></li>
								{else}
									<li><a title="Browse {$subcat.title}"
										   href="{$smarty.const.WWW_TOP}/browse?t={$subcat.ID}">{$subcat.title}</a></li>
								{/if}
							{/foreach}
						</ul>
					</li>
				{elseif ($parentcat.ID == 6000 && $userdata.xxxview=="1" && $site->lookupxxx=="1")}
					<li class="dropdown">
						<a id="cat3"
						   class="dropdown-toggle"
						   data-toggle="dropdown"
						   data-hover="dropdown"
						   href="{$smarty.const.WWW_TOP}/xxx">{$parentcat.title}
							<b class="caret"></b></a>
						<ul class="dropdown-menu" role="menu" aria-labelledby="cat3">
							<li><a href="{$smarty.const.WWW_TOP}/xxx">All {$parentcat.title}</a></li>
							{foreach from=$parentcat.subcatlist item=subcat}
								{if $subcat.ID == 6010 OR 6020 OR 6030 OR 6040}
									<li><a title="Browse {$subcat.title}"
										   href="{$smarty.const.WWW_TOP}/xxx?t={$subcat.ID}">{$subcat.title}</a>
									</li>
								{else}
									<li><a title="Browse {$subcat.title}"
										   href="{$smarty.const.WWW_TOP}/browse?t={$subcat.ID}">{$subcat.title}</a>
									</li>
								{/if}
							{/foreach}
						</ul>
					</li>
				{else}
					<li><a title="Browse {$parentcat.title}"
						   href="{$smarty.const.WWW_TOP}/browse?t={$parentcat.ID}">{$parentcat.title}</a>
						<ul>
							{foreach from=$parentcat.subcatlist item=subcat}
								{if ($subcat.ID == 7020 && $userdata.bookview=="1")}
									<li><a title="Browse {$subcat.title}"
										   href="{$smarty.const.WWW_TOP}/books">{$subcat.title}</a></li>
								{else}
									<li><a title="Browse {$subcat.title}"
										   href="{$smarty.const.WWW_TOP}/browse?t={$subcat.ID}">{$subcat.title}</a></li>
								{/if}
							{/foreach}
						</ul>
					</li>
				{/if}
			{/foreach}
			<li><a title="Browse All" href="{$smarty.const.WWW_TOP}/browse">All</a>
				<ul>
					<li><a title="Browse Groups" href="{$smarty.const.WWW_TOP}/browsegroup">Groups</a></li>
				</ul>
			</li>
		</ul>
	</div>

	<div id="menusearchlink">
		<form id="headsearch_form" action="{$smarty.const.WWW_TOP}/search/" method="get">

			<div class="gobutton" title="Submit search"><input id="headsearch_go" type="button" value="" tabindex="3"/>
			</div>

			<label style="display:none;" for="headcat">Search Category</label>
			<select id="headcat" name="t" tabindex="2">
				<option class="grouping" value="-1">All</option>
				{foreach from=$parentcatlist item=parentcat}
					<option {if $header_menu_cat==$parentcat.ID}selected="selected"{/if} class="grouping"
							value="{$parentcat.ID}">{$parentcat.title}</option>
					{foreach from=$parentcat.subcatlist item=subcat}
						<option {if $header_menu_cat==$subcat.ID}selected="selected"{/if} value="{$subcat.ID}">&nbsp;&nbsp;{$subcat.title}</option>
					{/foreach}
				{/foreach}
			</select>

			<label style="display:none;" for="headsearch">Search Text</label>
			<input id="headsearch" name="search"
				   value="{if $header_menu_search == ""}Enter keywords{else}{$header_menu_search|escape:"htmlall"}{/if}"
				   style="width:85px;" type="text" tabindex="1"/>

		</form>
	</div>
</div>
