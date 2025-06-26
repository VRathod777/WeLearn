<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * News items block class with Bootstrap-enhanced UI
 *
 * @package    block_news_items
 * @copyright  1999 onwards Martin Dougiamas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_news_items extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_news_items');
    }

    public function get_content() {
        global $CFG, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        if ($this->page->course->newsitems) {
            require_once($CFG->dirroot . '/mod/forum/lib.php');

            $text = '';

            if (!$forum = forum_get_course_forum($this->page->course->id, 'news')) {
                return '';
            }

            $modinfo = get_fast_modinfo($this->page->course);
            if (empty($modinfo->instances['forum'][$forum->id])) {
                return '';
            }

            $cm = $modinfo->instances['forum'][$forum->id];
            if (!$cm->uservisible) {
                return '';
            }

            $context = context_module::instance($cm->id);
            if (!has_capability('mod/forum:viewdiscussion', $context)) {
                return '';
            }

            $groupmode = groups_get_activity_groupmode($cm);
            $currentgroup = groups_get_activity_group($cm, true);

            if (forum_user_can_post_discussion($forum, $currentgroup, $groupmode, $cm, $context)) {
                $text .= '<div class="mb-3">
                            <a class="btn btn-sm btn-primary" href="' . $CFG->wwwroot . '/mod/forum/post.php?forum=' . $forum->id . '">
                                <i class="bi bi-plus-circle"></i> ' . get_string('addanewtopic', 'forum') . '
                            </a>
                          </div>';
            }

            $sort = forum_get_default_sort_order(true, 'p.modified', 'd', false);
            $discussions = forum_get_discussions($cm, $sort, false, -1, $this->page->course->newsitems, false, -1, 0, FORUM_POSTS_ALL_USER_GROUPS);

            if (!$discussions) {
                $text .= '<div class="text-muted">(' . get_string('nonews', 'forum') . ')</div>';
                $this->content->text = $text;
                return $this->content;
            }

            $strftimerecent = get_string('strftimerecent');

            foreach ($discussions as $discussion) {
                $discussion->subject = format_string($discussion->name, true, $forum->course);
                $posttime = (!empty($CFG->forum_enabletimedposts) && ($discussion->timestart > $discussion->modified))
                    ? $discussion->timestart : $discussion->modified;

                $userfullname = $discussion->userdeleted
                    ? get_string('deleteduser', 'mod_forum')
                    : fullname($discussion, has_capability('moodle/site:viewfullnames', $context));

                $text .= '
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body">
                            <h5>
                                <a class="text-decoration-none text-dark" href="' . $CFG->wwwroot . '/mod/forum/discuss.php?d=' . $discussion->discussion . '">
                                    <i class="bi bi-chat-dots-fill text-primary me-1"></i>' . $discussion->subject . '
                                </a>
                            </h5>
                            <p class="card-subtitle small text-muted mb-0">
                                <i class="bi bi-person-circle me-1"></i>' . $userfullname . '
                                &nbsp;&nbsp;
                                <i class="bi bi-clock me-1"></i>' . userdate($posttime, $strftimerecent) . '
                            </p>
                        </div>
                    </div>';
            }

            $this->content->text = $text;

            $this->content->footer = '<a class="btn btn-link p-0 mt-2 d-inline-block" href="' . $CFG->wwwroot . '/mod/forum/view.php?f=' . $forum->id . '">
                                        <i class="bi bi-clock-history"></i> ' . get_string('oldertopics', 'forum') . '
                                      </a>';

            if (!empty($CFG->enablerssfeeds) && !empty($CFG->forum_enablerssfeeds)
                && $forum->rsstype && $forum->rssarticles) {

                require_once($CFG->dirroot . '/lib/rsslib.php');

                $tooltiptext = $forum->rsstype == 1
                    ? get_string('rsssubscriberssdiscussions', 'forum')
                    : get_string('rsssubscriberssposts', 'forum');

                $userid = isloggedin() ? $USER->id : $CFG->siteguest;
                $this->content->footer .= '<br>' . rss_get_link($context->id, $userid, 'mod_forum', $forum->id, $tooltiptext);
            }
        }

        return $this->content;
    }
}
