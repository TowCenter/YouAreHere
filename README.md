You Are Here 

Basic instructions!

- To create a new page, create a folder with the same name as you want for the page, and add an 'index.html' as the content.

- header, navigation, sidebar and footer are all in the _includes folder. It's just plain HTML in there. We don't need to have all those things. We can keep or take out what we want in the default layout pages, which lives at _includes/themes/bootstrap-3/default.html. 

- Speaking of layouts, don't mess with anything in the _layouts folder. It's counter-intuitive, but the files in there are set up to pull in the bootstrap-3 theme, which is the actual place where we will edit the site layouts.

- To set the layout for a page on the website (any page), edit _includes/themes/bootstrap-3/page.html

- Likewise, to set the layout for a post, edit _inclues/themes/bootstrap-3/post.html

- the _posts folder is where blog posts live for the site. When you create a blog post, the file name needs to be in this format: YYYY-MM-DD-Title.md. For ex: 2015-07-25-Sample-Post.md. All posts are automatically available via the ruby var site.posts. More about that can be found in the jekyll documentation.

- The _site folder is where the jekyll puts the compiled HTML pages. This what's actually served to the public by github. You don't have to do anything with this folder and it's advisable to not make any edits here, as they will be overwritten the next time jekyll builds the site

- All CSS, Javascript and images for the site should be stored in assets/themes/bootstrap-3. Jekyll makes a variable called {{ ASSETS_PATH }} that paths directly to this folder. Check index.html to see it in action.

- CNAME.bak should be changed to CNAME if we wish to use our custom domain youarehere.network. I will also need to set some host records to point to github IPs if we go this route. For now, don't worry about it.

- You might notice some header info in some of these files, that start with three dashed lines, have some key : value pairs, and then end with three more dashed lines. This is called a 'Front Matter Block'. You can read more about that here: [http://jekyllrb.com/docs/frontmatter/](Front Matter)

Related links!
[http://jekyllrb.com/docs/home/](Jekyll Documentation)

Read through the documentation linked above, especially the topics under 'GETTING STARTED' and things should start to make sense!