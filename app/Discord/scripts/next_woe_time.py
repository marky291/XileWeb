import discord
import os
import requests
from datetime import datetime, timedelta
from discord.ext import tasks
import sys
import json

token = sys.argv[1]
url = sys.argv[2]
woe_times_data = json.loads(sys.argv[3])

# Convert any integers to strings and filter out invalid time strings
woe_times_data = {
    castle: str(time) for castle, time in woe_times_data.items()
    if isinstance(time, (str, int)) and (isinstance(time, str) and len(time) == 5 and time[2] == ':')
}

# Parse the valid time strings into datetime objects
woe_times_data = {castle: datetime.strptime(time, '%H:%M') for castle, time in woe_times_data.items()}

intents = discord.Intents.default()
intents.presences = True

client = discord.Client(intents=intents)
session = requests.Session()

@client.event
async def on_ready():
    print(f'Logged in as {client.user}')
    update_activity.start()

@tasks.loop(minutes=1)
async def update_activity():
    global woe_times_data  # Declare woe_times_data as a global variable

    # Get the current time
    current_time = datetime.now()

    # Subtract 1 hour from each WoE time
    woe_times_data = {castle: time + timedelta(hours=-1) for castle, time in woe_times_data.items()}

    # Find the next WoE time
    next_woe_time = min(woe_times_data.values())

    # Calculate the time difference in hours
    time_difference_hours = (next_woe_time - current_time).seconds // 3600

    # Format the time as a string
    time_str = current_time.strftime('%I:%M %p')

    # Display the time until the next WoE in hours along with the simplified next WoE time
    next_woe_str = f'({next_woe_time.strftime("%-I %p")})'  # %I without leading zero with a space
    activity_text = f'{time_difference_hours} hours {next_woe_str}'

    activity = discord.Activity(name=activity_text, type=discord.ActivityType.watching)

    await client.change_presence(activity=activity)

try:
    client.run(token)
except discord.HTTPException as e:
    if e.status == 429:
        print("Too many requests")
    else:
        raise e
