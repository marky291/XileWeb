# Server Commands Guide

Complete reference of all available commands on XileRO. All information sourced from the official XileRO wiki to ensure accuracy.

## Quick Reference for New Players

**Most Important Commands to Know:**
- `@commands` - See all available commands
- `@rates` - Check server rates
- `@go <city>` - Warp to major cities
- `@storage` - Access storage anywhere
- `@autoloot <percent>` - Auto-pickup items
- `@whereis <monster>` - Find monster locations
- `@whodrops <item>` - See what drops an item

---

## Utility Commands

Essential commands for everyday gameplay:

| Command | Description | Example |
|---------|-------------|---------|
| `@commands` | Display list of all available @ commands | `@commands` |
| `@security` | Set up account security to prevent item-related actions | `@security` |
| `@showexp` | Toggle experience gain message display on/off | `@showexp` |
| `@showzeny` | Toggle Zeny gain/loss message display | `@showzeny` |
| `@showdelay` | Show or hide skill delay messages | `@showdelay` |
| `@noask` | Toggle automatic rejection of trade/party invites | `@noask` |
| `@refresh` | Synchronize your character position with server | `@refresh` |
| `@help <command>` | Display detailed help for a specific command | `@help autoloot` |
| `@feelreset` | Reset Star Gladiator marked maps (SG only) | `@feelreset` |
| `@hatredreset` | Reset Star Gladiator marked monsters (SG only) | `@hatredreset` |
| `@jailtime` | Display remaining jail time if imprisoned | `@jailtime` |
| `@request <message>` | Send message to connected GMs | `@request Need help with bug` |

## Information Commands

Get server and character information:

| Command | Description | Example |
|---------|-------------|---------|
| `@rates` | Display current server rates (EXP, drop, etc.) | `@rates` |
| `@uptime` | Show server uptime and last restart | `@uptime` |
| `@time` | Show current server time and day/night info | `@time` |
| `@exp` | Display current level and experience progress | `@exp` |
| `@who` | List online characters with their locations | `@who` |
| `@who2` | List online characters with job classes | `@who2` |

## Movement & Teleportation

Travel quickly around the world:

| Command | Description | Example |
|---------|-------------|---------|
| `@go <location>` | Warp to predefined city locations | `@go prontera` |
| `@warp <map> <x> <y>` | Warp to specific map coordinates | `@warp prontera 150 150` |
| `@donation` | Warp directly to Donation NPC | `@donation` |

### Available @go Locations
- `prontera` - Prontera (main city)
- `geffen` - Geffen
- `payon` - Payon
- `alberta` - Alberta
- `aldebaran` - Al De Baran
- `izlude` - Izlude
- `morocc` - Morocc
- `comodo` - Comodo
- `yuno` - Juno
- `amatsu` - Amatsu
- `gonryun` - Kunlun
- `umbala` - Umbala
- `niflheim` - Niflheim
- `louyang` - Louyang
- `ayothaya` - Ayothaya
- `einbroch` - Einbroch
- `lighthalzen` - Lighthalzen
- `einbech` - Einbech
- `hugel` - Hugel
- `rachel` - Rachel
- `veins` - Veins
- `moscovia` - Moscovia

## Item & Storage Commands

Manage your items and inventory:

| Command | Description | Example |
|---------|-------------|---------|
| `@ii <item>` / `@iteminfo <item>` | Display detailed item information | `@ii red_potion` |
| `@storage` | Open Kafra storage from anywhere | `@storage` |
| `@guildstorage` | Open guild storage (guild members only) | `@guildstorage` |
| `@storeall` | Store all inventory items in Kafra storage | `@storeall` |
| `@autoloot <percent>` | Auto-loot items from killed monsters | `@autoloot 50` |
| `@alootid <+/-> <item_id>` | Add/remove specific item from autoloot list | `@alootid +909` |
| `@autoloottype <+/-> <type>` | Add/remove item type from autoloot | `@autoloottype +healing` |
| `@autotrade` | Continue vending while offline | `@autotrade` |

### Autoloot Tips
- Use `@autoloot 0` to disable autolooting
- Use `@autoloot 100` to loot everything
- `@alootid -909` removes item ID 909 from autoloot
- Common item types: `healing`, `usable`, `weapon`, `armor`, `card`

## Monster & Item Lookup

Research monsters and items:

| Command | Description | Example |
|---------|-------------|---------|
| `@mi <monster>` / `@mobinfo <monster>` | Display detailed monster information | `@mi poring` |
| `@whereis <monster>` | Show which maps a monster spawns on | `@whereis angeling` |
| `@whodrops <item>` | List monsters that drop specific item | `@whodrops red_potion` |

### Lookup Tips
- Use monster names or IDs: `@mi 1002` or `@mi poring`
- Use item names with underscores: `@whodrops red_potion`
- Monster info shows HP, EXP, stats, and drops

## Chat & Communication

Communicate with other players:

| Command | Description | Example |
|---------|-------------|---------|
| `@channel <action>` | Manage chat channels (join/leave/create) | `@channel create MyChannel` |
| `@channel join <name>` | Join a chat channel | `@channel join trade` |
| `@channel leave <name>` | Leave a chat channel | `@channel leave trade` |
| `@channel list` | List available channels | `@channel list` |

### Channel Actions
- `create <name>` - Create new channel
- `delete <name>` - Delete your channel
- `setcolor <name> <color>` - Set channel color
- `bind <name>` - Bind channel to a key

## Guild Commands

Guild management commands (for leaders/officers):

| Command | Description | Restrictions |
|---------|-------------|--------------|
| `@changegm <player>` | Transfer guild leadership | Guild leader only |
| `@breakguild` | Disband current guild | Guild leader only |

## Party Commands

Party management:

| Command | Description | Example |
|---------|-------------|---------|
| `@changeleader <player>` | Transfer party leadership | `@changeleader PlayerName` |
| `@partyoption <setting>` | Modify party item/EXP sharing | `@partyoption` |

## PvP & Duel Commands

Player versus player features:

### Duel System
| Command | Description | Example |
|---------|-------------|---------|
| `@duel <player>` | Challenge player to a duel | `@duel PlayerName` |
| `@invite <player>` | Invite player to your duel | `@invite PlayerName` |
| `@accept` | Accept duel invitation | `@accept` |
| `@reject` | Reject duel invitation | `@reject` |
| `@leave` | Leave current duel | `@leave` |

### Battlegrounds
| Command | Description | Example |
|---------|-------------|---------|
| `@joinbg` | Join Battlegrounds queue | `@joinbg` |
| `@leavebg` | Leave Battlegrounds | `@leavebg` |
| `@order <message>` | Send order to team members | `@order Attack the flag!` |
| `@reportafk <player>` | Report AFK player | `@reportafk PlayerName` |

## Light Graphical Plugin (LGP)

Enhanced visual features:

| Command | Description | Example |
|---------|-------------|---------|
| `@graphics` | Show available LGP commands | `@graphics` |
| `@lgp` | Enable/disable Light Graphical Plugin | `@lgp` |
| `@square <on/off/1-14>` | Display character position square | `@square on` |
| `@circle` | Display skill range circles | `@circle` |
| `@aoes` | Enable/disable AoE skill display | `@aoes` |

## Homunculus Commands

For Alchemist homunculus management:

| Command | Description | Example |
|---------|-------------|---------|
| `@hominfo` | Display homunculus statistics | `@hominfo` |
| `@homtalk <message>` | Make homunculus speak | `@homtalk Hello!` |

## Account Management

Secure account settings:

| Command | Description | Notes |
|---------|-------------|-------|
| `@myaccount` | Change account password | 24-hour waiting period applies |
| `@security` | Enable account protection | Prevents dropping/trading items |

## Advanced Commands

Special utility commands:

| Command | Description | Example |
|---------|-------------|---------|
| `@warp <map> <x> <y>` | Warp to exact coordinates | `@warp prontera 156 191` |
| `@storeall` | Store all items in Kafra storage | `@storeall` |

---

## Command Usage Guidelines

### Syntax Rules
- All commands start with `@` symbol
- Commands are **not** case-sensitive
- Required parameters shown in `<brackets>`
- Optional parameters shown in `[brackets]`
- Use underscores in item/monster names: `red_potion` not `red potion`

### Common Examples
```
@whereis poring              (Find Poring spawn locations)
@autoloot 25                (Auto-loot items 25% drop rate and lower)
@go prontera                (Warp to Prontera)
@alootid +909               (Add Jellopy to autoloot list)
@channel join trade         (Join the trade chat channel)
```

### Best Practices
- Use `@commands` to see full command list
- Commands execute instantly without confirmation
- Most commands work anywhere in the game
- Some commands have cooldowns to prevent spam
- Check your typing - misspelled commands won't work

### Common Mistakes to Avoid
- Forgetting the `@` symbol before commands
- Using spaces instead of underscores in names
- Trying to use GM-only commands as regular player
- Using wrong parameter syntax

## Command Categories by Usage

### **Essential for New Players**
`@commands`, `@rates`, `@go`, `@storage`, `@whereis`, `@whodrops`, `@autoloot`

### **Quality of Life**
`@exp`, `@time`, `@refresh`, `@showexp`, `@showzeny`, `@noask`

### **Research & Information**
`@mi`, `@ii`, `@who`, `@who2`, `@uptime`

### **Advanced Features**
`@security`, `@channel`, `@duel`, `@lgp`, `@hominfo`

---

## See Also

- [Server Rates](/wiki/server-info/rates) - Current server settings and rates
- [Getting Started](/wiki/getting-started/character-creation) - Basic gameplay guide  
- [Server Rules](/wiki/server-info/rules) - Important server guidelines
- [PvP Guide](/wiki/gameplay/pvp) - Player vs Player information
- [Guild System](/wiki/gameplay/guilds) - Guild features and commands

---

*Command availability may vary based on character level, job class, or special circumstances. Some commands may have usage restrictions or cooldowns.*

[← Back to Wiki Home](/wiki)