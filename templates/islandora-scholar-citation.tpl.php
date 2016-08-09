<?php
/*
 * islandora-scholar-citation.tpl.php
 * 
 *
 * 
 * This file overrides the default template provided by the islandora scholar module.
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with the program.  If not, see <http ://www.gnu.org/licenses/>.
 */
?>

<?php

if (isset($islandora_object->label))
{
  drupal_set_title($islandora_object->label);
}

?>

<div>
  <h3><?php print $islandora_object->label ?></h3>
</div>

<div id="tabs">

<ul>
  <li><a href="#tabs-1">Full Description</a></li>
  
  <?php if (isset($parent_mods_array)): ?>
    <li><a href="#tabs-2">Item Description</a></li>
    <li><a href="#tabs-3">Set Description</a></li>
  <?php elseif (isset($serial_mods_array)): ?>
    <li><a href="#tabs-2">Full Description</a></li>
    <li><a href="#tabs-3">Serial Details</a></li>
  <?php else: ?>
    <?php if (!$embargoed) { ?>
    <li><a href="#tabs-2">View Document</a></li>
    <?php } ?>
  <?php endif; ?>
</ul>

<div id="tabs-1">
    <div class="islandora-citation-image-sidebar">
      <div>
        <table class="islandora-table-display" width="100%">
        <tbody>
        <?php $row_field = 0; ?>
        <?php foreach($mods_array as $key => $value): ?>
          <?php if(trim($value['value']) != ''): ?>
            <tr class="islandora-definition-row <?php print $value['class']; ?>">
            <th class="full-description-heading<?php print $row_field == 0 ? ' first' : ''; ?>">
              <?php print $value['label']; ?>:
            </th>
            <td class="<?php print $value['class']; ?><?php print $row_field == 0 ? ' first' : ''; ?>">
              <?php print $value['value']; ?>
            </td>

            <?php if($row_field == 0): ?>
              <td class="islandora-basic-image-thumbnail" rowspan="8">

                  <?php if(isset($usage_views) && isset($usage_downloads)): ?>
                    <?php print "<span class=\"usage-stats-views\">Views: $usage_views</span>"; ?>
                    <?php print "<span class=\"usage-stats-downloads\">Downloads: $usage_downloads</span>"; ?>
                  <?php endif; ?>

                  <?php if(isset($islandora_full_url)): ?>
                    <?php print l($islandora_thumbnail_img, $islandora_full_url, array('html' => TRUE)); ?>

                  <?php elseif(isset($islandora_thumbnail_img)): ?>
                    <?php print '<img src="' . $islandora_thumbnail_img . '">'; ?>

                  <?php endif; ?>

                  <?php if (!$embargoed) { ?>
                    <?php if(isset($islandora_download_link)): ?>
                      <a href="<?php print $islandora_download_link; ?>">
                        <button class="download">Download PDF</button>
                      </a>
                    <?php endif; ?>

                  <?php } else { ?>
                  <button disabled class="download">Download PDF</button>
                  <p style="margin-top:10px;" class="error"><?php print $expiry_msg ?></p>

                  <?php } ?>

		    <div id="sharing-button-box">

                    <!-- Sharingbutton Facebook -->
                    <a class="resp-sharing-button__link" href="https://facebook.com/sharer/sharer.php?u=http%3A%2F%2Ftest.digital.flvc.org" target="_blank" aria-label="">
                      <div class="resp-sharing-button resp-sharing-button--facebook resp-sharing-button--small"><div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solid">
                        <svg version="1.1" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
                            <g>
                                <path d="M18.768,7.465H14.5V5.56c0-0.896,0.594-1.105,1.012-1.105s2.988,0,2.988,0V0.513L14.171,0.5C10.244,0.5,9.5,3.438,9.5,5.32 v2.145h-3v4h3c0,5.212,0,12,0,12h5c0,0,0-6.85,0-12h3.851L18.768,7.465z"/>
                            </g>
                        </svg>
                        </div>
                      </div>
                    </a>

                    <!-- Sharingbutton Twitter -->
                    <a class="resp-sharing-button__link" href="https://twitter.com/intent/tweet/?text=Example%20text!&amp;url=http%3A%2F%2Ftest.digital.flvc.org" target="_blank" aria-label="">
                      <div class="resp-sharing-button resp-sharing-button--twitter resp-sharing-button--small"><div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solid">
                        <svg version="1.1" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
                            <g>
                                <path d="M23.444,4.834c-0.814,0.363-1.5,0.375-2.228,0.016c0.938-0.562,0.981-0.957,1.32-2.019c-0.878,0.521-1.851,0.9-2.886,1.104 C18.823,3.053,17.642,2.5,16.335,2.5c-2.51,0-4.544,2.036-4.544,4.544c0,0.356,0.04,0.703,0.117,1.036 C8.132,7.891,4.783,6.082,2.542,3.332C2.151,4.003,1.927,4.784,1.927,5.617c0,1.577,0.803,2.967,2.021,3.782 C3.203,9.375,2.503,9.171,1.891,8.831C1.89,8.85,1.89,8.868,1.89,8.888c0,2.202,1.566,4.038,3.646,4.456 c-0.666,0.181-1.368,0.209-2.053,0.079c0.579,1.804,2.257,3.118,4.245,3.155C5.783,18.102,3.372,18.737,1,18.459 C3.012,19.748,5.399,20.5,7.966,20.5c8.358,0,12.928-6.924,12.928-12.929c0-0.198-0.003-0.393-0.012-0.588 C21.769,6.343,22.835,5.746,23.444,4.834z"/>
                            </g>
                        </svg>
                        </div>
                      </div>
                    </a>

                    <!-- Sharingbutton Google+ -->
                    <a class="resp-sharing-button__link" href="https://plus.google.com/share?url=http%3A%2F%2Ftest.digital.flvc.org" target="_blank" aria-label="">
                      <div class="resp-sharing-button resp-sharing-button--google resp-sharing-button--small"><div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solid">
                        <svg version="1.1" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
                            <g>
                                <path d="M11.366,12.928c-0.729-0.516-1.393-1.273-1.404-1.505c0-0.425,0.038-0.627,0.988-1.368 c1.229-0.962,1.906-2.228,1.906-3.564c0-1.212-0.37-2.289-1.001-3.044h0.488c0.102,0,0.2-0.033,0.282-0.091l1.364-0.989 c0.169-0.121,0.24-0.338,0.176-0.536C14.102,1.635,13.918,1.5,13.709,1.5H7.608c-0.667,0-1.345,0.118-2.011,0.347 c-2.225,0.766-3.778,2.66-3.778,4.605c0,2.755,2.134,4.845,4.987,4.91c-0.056,0.22-0.084,0.434-0.084,0.645 c0,0.425,0.108,0.827,0.33,1.216c-0.026,0-0.051,0-0.079,0c-2.72,0-5.175,1.334-6.107,3.32C0.623,17.06,0.5,17.582,0.5,18.098 c0,0.501,0.129,0.984,0.382,1.438c0.585,1.046,1.843,1.861,3.544,2.289c0.877,0.223,1.82,0.335,2.8,0.335 c0.88,0,1.718-0.114,2.494-0.338c2.419-0.702,3.981-2.482,3.981-4.538C13.701,15.312,13.068,14.132,11.366,12.928z M3.66,17.443 c0-1.435,1.823-2.693,3.899-2.693h0.057c0.451,0.005,0.892,0.072,1.309,0.2c0.142,0.098,0.28,0.192,0.412,0.282 c0.962,0.656,1.597,1.088,1.774,1.783c0.041,0.175,0.063,0.35,0.063,0.519c0,1.787-1.333,2.693-3.961,2.693 C5.221,20.225,3.66,19.002,3.66,17.443z M5.551,3.89c0.324-0.371,0.75-0.566,1.227-0.566l0.055,0 c1.349,0.041,2.639,1.543,2.876,3.349c0.133,1.013-0.092,1.964-0.601,2.544C8.782,9.589,8.363,9.783,7.866,9.783H7.865H7.844 c-1.321-0.04-2.639-1.6-2.875-3.405C4.836,5.37,5.049,4.462,5.551,3.89z"/>
                                <polygon points="23.5,9.5 20.5,9.5 20.5,6.5 18.5,6.5 18.5,9.5 15.5,9.5 15.5,11.5 18.5,11.5 18.5,14.5 20.5,14.5 20.5,11.5  23.5,11.5 	"/>
                            </g>
                        </svg>
                        </div>
                      </div>
                    </a>

                    <!-- Sharingbutton E-Mail -->
                    <a class="resp-sharing-button__link" href="mailto:?subject=Example%20text!&amp;body=http%3A%2F%2Ftest.digital.flvc.org" target="_self" aria-label="">
                      <div class="resp-sharing-button resp-sharing-button--email resp-sharing-button--small"><div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solid">
                        <svg version="1.1" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
                            <path d="M22,4H2C0.897,4,0,4.897,0,6v12c0,1.103,0.897,2,2,2h20c1.103,0,2-0.897,2-2V6C24,4.897,23.103,4,22,4z M7.248,14.434 l-3.5,2C3.67,16.479,3.584,16.5,3.5,16.5c-0.174,0-0.342-0.09-0.435-0.252c-0.137-0.239-0.054-0.545,0.186-0.682l3.5-2 c0.24-0.137,0.545-0.054,0.682,0.186C7.571,13.992,7.488,14.297,7.248,14.434z M12,14.5c-0.094,0-0.189-0.026-0.271-0.08l-8.5-5.5 C2.997,8.77,2.93,8.46,3.081,8.229c0.15-0.23,0.459-0.298,0.691-0.147L12,13.405l8.229-5.324c0.232-0.15,0.542-0.084,0.691,0.147 c0.15,0.232,0.083,0.542-0.148,0.691l-8.5,5.5C12.189,14.474,12.095,14.5,12,14.5z M20.934,16.248 C20.842,16.41,20.673,16.5,20.5,16.5c-0.084,0-0.169-0.021-0.248-0.065l-3.5-2c-0.24-0.137-0.323-0.442-0.186-0.682 s0.443-0.322,0.682-0.186l3.5,2C20.988,15.703,21.071,16.009,20.934,16.248z"/>
                        </svg>
                        </div>
                      </div>
                    </a>
		    </div>

              </td>

            <?php endif; ?>
            </tr>
            <?php $row_field++; ?>
          <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
        </table>
      </div>
    </div>
</div>

<div id="tabs-2">
  <div class="islandora-citation-object islandora">
    <div class="islandora-citation-content-wrapper clearfix">
      <?php if(isset($citation_view)): ?>
        <div class="islandora-citation-content">
          <p><embed height="600" src="<?php print $citation_view ?>" width="100%"></embed></p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

</div>
<div class="islandora-object-branding">
  <ul>
    <!--- START OTHERLOGO DISPLAY -->
    <?php $local_counter = 0; ?>
    <?php while (isset($branding_info['other_logo_' . $local_counter]) && $local_counter < 3): ?>
      <li><a href="<?php print $branding_info['other_logo_' . $local_counter]['institution_link'] ?>" target="_blank">  	
      <img src="<?php print base_path() . variable_get('file_public_path', conf_path() . '/files') . '/custom_logos/' . $branding_info['other_logo_' . $local_counter]['image_filename'] ?>"></a>
      </li>
      <?php $local_counter++; ?>
    <?php endwhile; ?>
    <!--- END OF OTHERLOGO DISPLAY -->      
    
    <li><a href="<?php print $branding_info['institution_logo']['institution_link'] ?>" target="_blank">
    <img src="<?php print base_path() . variable_get('file_public_path', conf_path() . '/files') . '/custom_logos/' . $branding_info['institution_logo']['image_filename'] ?>"></a>
    </li>
  </ul>
</div>
