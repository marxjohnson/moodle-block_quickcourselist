M.block_quickcourselist = {
    init: function(Y, instanceid) {
        this.Y = Y;
        this.instanceid = instanceid;

        this.progress = Y.one('#quickcourseprogress');
        this.xhr = null;

        Y.one('#quickcourselistsearch').on('keyup', function(e) {
            var searchstring = e.target.get('value');
            M.block_quickcourselist.search(searchstring);
        });
        Y.one('#quickcourseform').('submit', function(e) {
            e.preventDefault();
            var searchstring = e.target.getById('quickcourselistsearch').get('value');
            M.block_quickcourselist.search(searchstring);
        });
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
                    list = Y.Node.create('<ul />');
                    if (courses.length > 0) {
                        Y.Array.each(courses, function(course) {
                            Y.Node.create('<li><a href="'+M.cfg.wwwroot+'/course/view.php?id='+course.id+'">'+course.shortname+' '+course.fullname+'</a></li>').appendTo(list);
                        });
                    }
                    Y.one('#quickcourselist').replace(list);
                    list.setAttribute('id', 'quickcourselist');
                    block.progress.setStyle('visibility', 'hidden');
                },
                failure: function(id, o) {
                    if (o.statusText != 'abort') {
                        var block = M.block_quickcourselist;
                        block.progress.setStyle('visibility', 'hidden');
                        if (o.statusText !== undefined) {
                            var list = Y.Node.create('<p>'+o.statusText+'</p>');
                            Y.one('#quickcourselist').replace(list);
                            list.set('id', 'quickcourselist');
                        }
                    }
                }
            }
        });
    }
}
