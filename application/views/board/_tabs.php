			
			<div data-type="tabs">
				<ul>
					<li class="active"><a href="<?=URL::to('board/' . $board->alias)?>">전체 보기</a></li>
				
					<? if (Authly::signed()): ?>
				
					<li><a href="<?=$board->link('by/' . Authly::get_userid())?>">내가 쓴 글</a></li>
				
					<? endif; ?>
					
					<? if ($board->series_enabled()): ?>
				
					<li><a href="<?=$board->link('series')?>">연재물</a></li>
					
					<? endif; ?>
					
					<? if (Authly::signed()): ?>
					
					<li><a href="<?=$board->link()?>/drafts">임시보관함 <?if($board->draft_count(Authly::get_id())):?><span>(<?=$board->draft_count(Authly::get_id())?>)</span><?endif;?></a></li>
					
					<? endif; ?>
					
					<? if (isset($board->author_tab)): ?>
					
					<li><a href="<?=$board->link()?>/by/<?=$board->author_tab['userid']?>"><?=$board->author_tab['name']?></a></li>					
					
					<? endif; ?>
					
					
					<? if ( ! $board->locked()): ?>
					
					<li data-align="right"><a href="<?=$board->link()?>/new">새 글 쓰기</a></li>
					
					<? endif; ?>
					
				</ul>
				
				<div></div>
			</div>