			
			<div data-ui="tabs">
				<ul>
					<li<?=($active_tab == 'all' ? ' class="active"' : '')?>><a href="<?=URL::to('board/' . $board->alias)?>">전체 보기</a></li>
				
					<? if (Authly::signed()): ?>
				
					<li<?=($active_tab == 'my' ? ' class="active"' : '')?>><a href="<?=$board->link('by/' . Authly::get_userid())?>">내 글</a></li>
				
					<? endif; ?>
					
					<? if ($board->series_enabled()): ?>
				
					<li<?=($active_tab == 'series' ? ' class="active"' : '')?>><a href="<?=$board->link('series')?>">연재물</a></li>
					
					<? endif; ?>
					
					<? if (Authly::signed()): ?>
					
					<li<?=($active_tab == 'draft' ? ' class="active"' : '')?>><a href="<?=$board->link()?>/drafts">보관함 <?if($board->draft_count(Authly::get_id())):?><span>(<?=$board->draft_count(Authly::get_id())?>)</span><?endif;?></a></li>
					
					<? endif; ?>
					
					<? if (isset($board->author_tab)): ?>
					
					<li<?=($active_tab == 'author' ? ' class="active"' : '')?>><a href="<?=$board->link()?>/by/<?=$board->author_tab['userid']?>"><?=$board->author_tab['name']?></a></li>					
					
					<? endif; ?>
					
					
					<? if ( ! $board->locked()): ?>
					
					<li<?=($active_tab == 'new' ? ' class="active"' : '')?> data-align="right"><a href="<?=$board->link()?>/new">글쓰기</a></li>
					
					<? endif; ?>
					
				</ul>
				
				<div></div>
			</div>