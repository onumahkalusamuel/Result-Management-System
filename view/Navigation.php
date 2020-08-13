<?php $activeLink = basename($_SERVER['QUERY_STRING']); ?>
<?php $routeBase = \Core\Config::BASEURL; ?>

<nav>
    <li><a class="<?=($activeLink=='admin'? 'active': null);?>" href='<?=$routeBase?>admin'>Admin</a></li> /
    <li><a class="<?=($activeLink=='class-category'? 'active': null);?>" href='<?=$routeBase?>class-category'>Class Category</a></li> /
    <li><a class="<?=(in_array($activeLink, ['students', 'upgrade']) ? 'active': null);?>" href='<?=$routeBase?>students'>Students</a></li> /
    <li><a class="<?=($activeLink=='subjects'? 'active': null);?>" href='<?=$routeBase?>subjects'> Subjects</a></li> /
    <li><a class="<?=(in_array($activeLink, ['grading','grading-system']) ? 'active': null);?>" href='<?=$routeBase?>grading'>Grading</a></li> /
    <li><a class="<?=($activeLink=='examination'? 'active': null);?>" href='<?=$routeBase?>examination'>Examination</a></li> /
    <li><a class="<?=(in_array($activeLink, ['papers', 'allpapers'])? 'active': null);?>" href='<?=$routeBase?>papers'>Papers</a></li> /
    <li><a class="<?=(in_array($activeLink, ['scores', 'scoresheet']) ? 'active': null);?>" href='<?=$routeBase?>scores'>Scores</a></li> /
    <li><a class="<?=($activeLink=='results'? 'active': null);?>" href='<?=$routeBase?>results'>Results</a></li> /
    <li><a class="" style="border:1px solid white; padding:0px 5px; color: #9d1717; background: white" href='<?=$routeBase?>logout'>Logout</a></li>
</nav>