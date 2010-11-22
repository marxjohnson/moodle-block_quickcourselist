M.block_quickcourselist = {
    init: function(Y, instanceid) {
        this.Y = Y;
        this.instanceid = instanceid;
                
        this.progress = Y.one('#quickcourseprogress');
        this.listcontainer = Y.one('#quickcourselist');
        this.searchbox = Y.one('#quickcourselistsearch');
        this.xhr = null;
        
        Y.on('keyup', this.search_on_type, '#quickcourselistsearch');
        Y.on('submit', this.search_on_submit, '#quickcourseform');
    },

    search_on_type: function(e) {
        var searchstring = e.target.get('value');
        M.block_quickcourselist.search(searchstring);
    },

    search_on_submit: function(e) {
        e.preventDefault();
        var searchstring = e.target.getById('quickcourselistsearch').get('value');
        M.block_quickcourselist.search(searchstring);
    },

    search: function(string) {
        
        var Y = this.Y;
        uri = M.cfg.wwwroot+'/blocks/quickcourselist/quickcourse.php';
        if (this.xhr != null) {
            this.xhr.abort();
        }
        this.progress.setStyle('visibility', 'visible');
        this.xhr = Y.io(uri, {
            data: 'course='+string+'&instanceid='+this.instanceid,
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
