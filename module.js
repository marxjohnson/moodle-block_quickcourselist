M.block_quickcourselist = {
    init: function(Y, instanceid) {
        this.Y = Y;
        this.instanceid = instanceid;
                
        this.progress = Y.one('#quickcourseprogress');
        this.listcontainer = Y.one('#quickcourselist');
        this.searchbox = Y.one('#quickcourselistsearch');
        this.xhr = null;
        
        Y.on('keyup', this.search, '#quickcourselistsearch');
        Y.on('submit', this.search, '#quickcourseform');
    },

    search: function(e) {
        e.preventDefault();
        var block = M.block_quickcourselist;
        var Y = M.block_quickcourselist.Y;
        var searchstring = block.searchbox.get('value');

        uri = M.cfg.wwwroot+'/blocks/quickcourselist/quickcourse.php';
        if (block.xhr != null) {
            block.xhr.abort();
        }
        block.progress.setStyle('visibility', 'visible');
        block.xhr = Y.io(uri, {
            data: 'course='+searchstring+'&instanceid='+M.block_quickcourselist.instanceid,
            on: {
                success: function(id, o) {
                    var courses = Y.JSON.parse(o.responseText);
                    var block = M.block_quickcourselist;
                    var list = '';
                    if (courses.length > 0) {
                        list = '<ul>';
                        for (c in courses) {
                            list += '<li><a href="'+M.cfg.wwwroot+'/course/view.php?id='+courses[c].id+'">'+courses[c].shortname+' '+courses[c].fullname+'</a></li>';
                        }
                        list += '</ul>';                        
                    }
                    block.listcontainer.set('innerHTML', list);
                    block.progress.setStyle('visibility', 'hidden');
                },
                failure: function(id, o) {
                    if (o.statusText != 'abort') {
                        var block = M.block_quickcourselist;
                        block.progress.setStyle('visibility', 'hidden');
                        if (o.statusText !== undefined) {
                            block.listcontainer.set('innerHTML', o.statusText);
                        }
                    }
                }
            }
        });
    }
}
