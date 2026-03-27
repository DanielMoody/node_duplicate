# Node Duplicate

Adds a simple way to duplicate content in Drupal.

- Duplicate a single node from the content list  
- Duplicate multiple nodes using bulk actions  
- Creates unpublished copies with a “(Copy)” suffix  

---

## Features

### Row-level duplicate
Adds a **Duplicate** link to each node on `/admin/content`.

### Safe defaults
- Copies all field values  
- Sets the current user as author  
- Marks duplicates as unpublished  

## Behavior

- Title becomes: Original Title (Copy)
- Status: - Unpublished (if supported)

- Author: - Set to the current user

- References: - Entity references are reused, not deep-copied

---

## Limitations

- Designed for **nodes only**  
- Does not deep-copy referenced entities (e.g. media, paragraphs)  
- Does not handle unique field constraints automatically  
- Does not copy revision history  

---

## Notes

This module uses Drupal’s built-in Entity::createDuplicate(). No custom duplication logic is implemented.

