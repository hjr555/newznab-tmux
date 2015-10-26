<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:newznab="http://www.newznab.com/DTD/2010/feeds/attributes/" encoding="utf-8">
	<channel>
		<atom:link href="{$smarty.const.WWW_TOP}api" rel="self" type="application/rss+xml" />
		<title>{$site->title|escape}</title>
		<description>{$site->title|escape} API Results</description>
		<link>{$smarty.const.WWW_TOP}</link>
		<language>en-gb</language>
		<webMaster>{$site->email} ({$site->title|escape})</webMaster>
		<category>{$site->meta_keywords}</category>
		<image>
			<url>{$smarty.const.WWW_TOP}themes/nntmux/images/logo.png</url>
			<title>{$site->title|escape}</title>
			<link>{$smarty.const.WWW_TOP}</link>
			<description>Visit {$site->title|escape} - {$site->strapline|escape}</description>
		</image>
		<newznab:response offset="{$offset}" total="{if $releases|@count > 0}{$releases[0]._totalrows}{else}0{/if}" />
		{foreach from=$releases item=release}
			<item>
				<title>{$release.searchname|escape:html}</title>
				<guid isPermaLink="true">{$smarty.const.WWW_TOP}details/{$release.guid}</guid>
				<link>{$smarty.const.WWW_TOP}getnzb/{$release.guid}.nzb&amp;i={$uid}&amp;r={$rsstoken}</link>
				<comments>{$smarty.const.WWW_TOP}details/{$release.guid}#comments</comments>
				<pubDate>{$release.adddate|phpdate_format:"DATE_RSS"}</pubDate>
				<category>{$release.category_name|escape:html}</category>
				<description>{$release.searchname|escape:html}</description>
				<enclosure url="{$smarty.const.WWW_TOP}getnzb/{$release.guid}.nzb&amp;i={$uid}&amp;r={$rsstoken}" length="{$release.size}" type="application/x-nzb" />
				{foreach from=$release.category_ids|parray:"," item=cat}
					<newznab:attr name="category" value="{$cat}" />
				{/foreach}
				<newznab:attr name="size" value="{$release.size}" />
				{if isset($release.coverurl) && $release.coverurl != ""}
					<newznab:attr name="coverurl" value="{$smarty.const.WWW_TOP}covers/{$release.coverurl}" />
				{/if}
				{if $extended == "1"}
					<newznab:attr name="files" value="{$release.totalpart}" />
					<newznab:attr name="poster" value="{$release.fromname|escape:html}" />
					{if $release.season != ""}
						<newznab:attr name="season" value="{$release.season}" />
					{/if}
					{if $release.episode != ""}
						<newznab:attr name="episode" value="{$release.episode}" />
					{/if}
					{if $release.videos_id != "-1" && $release.videos_id != "-2"}
						<newznab:attr name="videos_id" value="{$release.videos_id}" />
					{/if}
					{if $release.tvtitle != ""}
						<newznab:attr name="tvtitle" value="{$release.tvtitle|escape:html}" />
					{/if}
					{if $result.firstaired != ""}
						<newznab:attr name="firstaired" value="{$result.firstaired|phpdate_format:"DATE_RSS"}" />
					{/if}
					{if $release.imdbid != ""}
						<newznab:attr name="imdb" value="{$release.imdbid}" />
					{/if}
					<newznab:attr name="grabs" value="{$release.grabs}" />
					<newznab:attr name="comments" value="{$release.comments}" />
					<newznab:attr name="password" value="{$release.passwordstatus}" />
					<newznab:attr name="usenetdate" value="{$release.postdate|phpdate_format:"DATE_RSS"}" />
					<newznab:attr name="group" value="{$release.group_name|escape:html}" />
				{/if}
			</item>
		{/foreach}
	</channel>
</rss>