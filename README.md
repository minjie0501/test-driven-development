# Test Driven Development

## The mission
Explore and learn about Unit Tests and Test Driven Development.

### Must have features
- [x] Create a User, Room and Booking entity
- [x] Setup a database using Doctrine
- [x] Implement and write tests for the following conditions:
    - Rooms marked as premium can only be hired for premium members
    - No room can be booked for more than 4 hours
    - Check if they can afford the rent for the room
    - Room can only be booked if no other User has already booked it in this time (this is the most difficult condition)