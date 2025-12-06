<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(0);
?>
<nav>
	<ul class="pager">
		<li <?= $pager->hasPrevious() ? '' : 'class="disabled"' ?>>
			<a href="<?= $pager->getPrevious() ?? '#' ?>" aria-label="<?= lang2('Pager.previous') ?>">
				<span aria-hidden="true"><?= lang2('Pager.newer') ?></span>
			</a>
		</li>
		<li <?= $pager->hasNext() ? '' : 'class="disabled"' ?>>
			<a href="<?= $pager->getNext() ?? '#' ?>" aria-label="<?= lang2('Pager.next') ?>">
				<span aria-hidden="true"><?= lang2('Pager.older') ?></span>
			</a>
		</li>
	</ul>
</nav>
