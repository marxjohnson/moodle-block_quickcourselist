M.block_quickcourselist = {
    init: function(Y, instanceid) {
        this.instanceid = instanceid;

        this.progress = $('#quickcourseprogress');
        this.xhr = null;

        $('#quickcourselistsearch').keyup(function(e) {
            var searchstring = e.target.value;
            M.block_quickcourselist.search(searchstring);
        });
        $('#quickcourseform').submit(function(e) {
            e.preventDefault();
            var searchstring = $('#quickcourselistsearch').get('value');
            M.block_quickcourselist.search(searchstring);
        });
    },

    search: function(string) {

        uri = M.cfg.wwwroot+'/blocks/quickcourselist/quickcourse.php';
        if (this.xhr != null) {
            this.xhr.abort();
        }
        this.progress.css('visibility', 'visible');
        this.xhr = $.ajax(uri, {
            data: 'course='+string+'&instanceid='+this.instanceid,
            context: M.block_quickcourselist,
            success: function(courses, status) {
                list = $('<ul />');
                if (courses.length > 0) {
                    $.each(courses, function(key, course) {
                        $('<li><a href="'+M.cfg.wwwroot+'/course/view.php?id='+course.id+'">'+course.shortname+' '+course.fullname+'</a></li>').appendTo(list);
                    });
                }
                $('#quickcourselist').replaceWith(list);
                list.attr('id', 'quickcourselist');
                this.progress.css('visibility', 'hidden');
            },
            error: function(o, status) {
                if (status != 'abort') {
                    this.progress.css('visibility', 'hidden');
                    if (status !== undefined) {
                        var list = $('<p>'+status+'</p>');
                        $('#quickcourselist').replace(list);
                        list.attr('id', 'quickcourselist');
                    }
                }
            }
        });
    }
}
