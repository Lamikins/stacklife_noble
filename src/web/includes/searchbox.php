<div class="header group">
	<div class="header-content group">
		<div class="search">
			<form id="search" method="get" action="<?php echo $www_root?>/search">
            	<input type="hidden" style="display:none" name="search_type" value="keyword"/>
            	<input type="text" name="q" autofocus="autofocus" placeholder="Search"/>
            	<input type="submit" name="submit_search" id="itemsearch" value="Go!"/>
			</form>
			<a id="inline" href="#advanced" style="display:none">Advanced Search</a>

			<a href="<?php echo $www_root?>/search?advanced=true" class="button advanced-search">Advanced Search</a>
			
    	</div><!--end search-->
	</div><!--end header-content-->
</div><!--end header-->

<!-- Advanced search fancybox, start -->
<div style="display:none">
	<div id="advanced">
		<form method="get" action="<?php echo $www_root?>/search">

		<div class="left advanced-inputs">
			<div class="facet_set">
				<div class="heading">Advanced search terms</div>
			</div>
              <p>
              	<select name="search_type">
                      <option value="isbn">ISBN</option>
                      <option value="title">Title contains keyword(s)</option>
                      <option value="author">Author contains keyword(s)</option>
                      <option value="keyword" selected="selected">Keyword(s) anywhere</option>
                  </select>
                  <input type="text" class="searchBox" name="q"/>
                  		  <input type="submit" name="submit_search" value="Go!"/>
              </p>
              <!--<p id="addremove"><span class="addfield">add</span> / <span class="removefield">remove</span></p>-->
          </div>
        </form>
	</div>
</div>
<!-- Advanced search fancybox, end -->