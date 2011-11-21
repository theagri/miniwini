			
			<div data-type="tabs">
				<ul>
					<li class="active"><a href="<?=URL::to('board/' . $board->alias)?>">전체 보기</a></li>
				
					<? if (Authly::signed()): ?>
				
					<li><a href="<?=$board->link('by/' . Authly::get_userid())?>">내가 쓴 글</a></li>
				
					<? endif; ?>
				
					<li><a href="<?=$board->link('series')?>">연재물</a></li>
					
					<? if (Authly::signed()): ?>
					
					<li data-tab="tab-draft"><a href="<?=$board->link?>/drafts">임시보관함 <?if($board->draft_count(Authly::get_id())):?><span>(<?=$board->draft_count(Authly::get_id())?>)</span></a><?endif;?></li>
					
					<? endif; ?>
					
					<li data-tab="tab-newpost"><a href="<?=$board->link?>/new">새 글 쓰기</a></li>
				</ul>
				
				<div></div>
			</div>