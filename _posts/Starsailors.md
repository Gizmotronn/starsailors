---
layout: post
title: Star Sailors
description: >
  Hydejack is a pretentious two-column [Jekyll](http://jekyllrb.com) theme,
  stolen by [@qwtel](https://twitter.com/qwtel) from [Hyde](http://hyde.getpoole.com).
  You could say it was.. [hydejacked](http://media3.giphy.com/media/makedRIckZBW8/giphy.gif).
excerpt_separator: <!--more-->
---

<div class="codegena_iframe" data-src="http://allianceofdroids.org.au" style="height:471px;width:800px;" data-responsive="true" data-img="http://blog.allianceofdroids.org.au/wp-content/uploads/2019/02/ACORD.v1.png" data-css="background:url('//codegena.com/wp-content/uploads/2015/09/loading.gif') white center center no-repeat;border:0px;"></div><script src="https://rawgit.com/shaneapen/Codegena/master/async-iframe.js"></script>

~~~yml
layout: post
title: Introducing Star Sailors
image: http://blog.allianceofdroids.org.au/wp-content/uploads/2019/02/ACORD.v1.png
color: '#949667'
~~~

### How to Add a New Tag
Tags are not meant to be used #instagram #style: #food #goodfood #happy #happylife #didimentionfood #yougetthepoint,
as each tag requires some setup work. I tend to think of it as categories that can be combined.

1.  Add an entry to `_data/tags.yml`, where the key represents a slug and provide at least a `name` value and
    optionally `image`, `color` and `description`.

    Example `/_data/tags.yml`:

    ~~~yml
    mytag:
      name: My Tag
    ~~~

2.  Make a new file in the `tag` folder, using the same name you've used as the key / slug and
    change the `tag` and `permalink` entries.

    Example `/tag/mytag.md`:

    ~~~yml
    layout: blog-by-tag
    tag: mytag
    permalink: /tag/mytag/
    ~~~

3.  Tag your blog posts using the `tags` key (color and image will only depend on the first tag).

    ~~~yml
    layout: post
    title: Introducing My New Tag
    tags: [mytag, othertag]
    ~~~

4. (optional) Add the tag to the sidebar, by adding it to `sidebar_tags` in `_config.yml`.
   They will appear in the listed order.

   ~~~yml
   sidebar_tags: [mytag, othertag]
   ~~~


[docs]: ../../docs/README.md
[tag]: http://www.minddust.com/post/tags-and-categories-on-github-pages/
