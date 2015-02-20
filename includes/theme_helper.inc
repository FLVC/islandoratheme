<?php

/**
 * @file
 * A collection of helper functions that process data for display
 */

// Parses the RELATED-LINKS XML file and turns it into HTML
function create_related_links_html($islandora_object) {
  $related_links_html = '<div id="related-links-section">';
  $related_links_xml_object = simplexml_load_string($islandora_object['RELATED-LINKS']->content);
  
  if (isset($related_links_xml_object['title']))
  {
    $related_links_html .= '<h2>' . $related_links_xml_object['title'] . '</h2>';
  }
  else
  {
    $related_links_html .= '<h2>Related Links</h2>';
  }

  foreach ($related_links_xml_object->children() as $xml_child) {
    
    if ($xml_child->getName() == 'external-link')
    {
      $related_links_html .= '<li><a href="' . $xml_child . '" target="_blank">';
      
      if (isset($xml_child['title']))
      {
        $related_links_html .= $xml_child['title'] . '</a></li>';
      }
      else
      {
        $related_links_html .= $xml_child . '</a></li>';
      }
    }
    
    if($xml_child->getName() == 'internal-link')
    {
      $related_links_html .= '<li><a href="' . $xml_child . '">';
      
      if (isset($xml_child['title']))
      {
        $related_links_html .= $xml_child['title'] . '</a></li>';
      }
      else
      {
        $related_links_html .= $xml_child . '</a></li>';
      }
    }
    
    if($xml_child->getName() == 'page')
    {
      $related_links_html .= '<li><a href="#' . $xml_child . '">';
      
      if (isset($xml_child['title']))
      {
        $related_links_html .= $xml_child['title'] . '</a></li>';
      }
      else
      {
        $related_links_html .= $xml_child . '</a></li>';
      }
      
      $related_links_html .= '<div id="' . $xml_child . '" class="modalCSSPage">';
      
      //retrieve page html
      if(isset($islandora_object[(string)$xml_child]))
      {
        $related_links_html .= '<div>' . '<a href="#close" title="Close" class="closeModal">X</a>'; 
        $related_links_html .= $islandora_object[(string)$xml_child]->content . '</div>';
      }
      
      $related_links_html .= '</div>';
    }
  }
  
  $related_links_html .= '</div>';
  
  return $related_links_html;
}
