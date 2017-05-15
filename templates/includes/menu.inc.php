        <ul>
            <?
            $query=@mysqli_query($mysql, "SELECT * FROM `pages` WHERE `pid`='0' AND `published`='1' AND `visible`='1' ORDER BY `order` ASC");
            while ($row=@mysqli_fetch_assoc($query)) {
                ?>
                <li class="<? if (in_array(@$row["id"], $pagesarr)!==FALSE) echo "active";?>">
                    <a href="/<?=@$row["rewrite"]=='/'?'':@$row["rewrite"]?>"><?=$row["title"]?></a>
                    <?
                    $query1=@mysqli_query($mysql,"select * from `pages` where `pid`='".@$row["id"]."' and `published`='1' and `visible`='1' order by `order` asc");
                    if(@mysqli_num_rows($query1)){
                        ?>
                        <ul>
                            <?
                            while($row1=@mysqli_fetch_assoc($query1)){
                                ?>
                                <li class="<? if (in_array(@$row1["id"], $pagesarr)!==FALSE) echo "active";?>">
                                    <a href="/<?=@$row1["rewrite"]=='/'?'':@$row1["rewrite"]?>"><?=$row1["title"]?></a>
                                    <?
                                    $query2=@mysqli_query($mysql,"select * from `pages` where `pid`='".@$row1["id"]."' and `published`='1' and `visible`='1' order by `order` asc");
                                    if(@mysqli_num_rows($query2)){
                                        ?>
                                        <ul>
                                            <?
                                            while($row2=@mysqli_fetch_assoc($query2)){
                                                ?>
                                                <li class="<? if (in_array(@$row2["id"], $pagesarr)!==FALSE) echo "active";?>">
                                                    <a href="/<?=@$row2["rewrite"]=='/'?'':@$row2["rewrite"]?>"><?=$row2["title"]?></a>
                                                </li>
                                                <?
                                            }
                                            ?>
                                        </ul>
                                        <?
                                    }
                                    ?>
                                </li>
                                <?
                            }
                            ?>
                        </ul>
                    <?
                    }
                    ?>
                </li>
                <?
            }
            ?>
        </ul>
